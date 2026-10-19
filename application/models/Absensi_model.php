<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

	/**
	 * Simpan absensi + progres satu pertemuan untuk semua peserta (transaksi).
	 * Satu form per pertemuan: peserta yang sudah ada di-update, sisanya di-insert.
	 *
	 * @param array $rows [{peserta_id, status, hal_mulai, hal_selesai, catatan_guru}, ...]
	 * @return bool
	 */
	public function simpanBulk($pertemuanId, array $rows)
	{
		if (! $rows) {
			return FALSE;
		}

		// Satu query untuk semua baris eksis (bukan loop cek per baris).
		$ada = $this->db->select('id, peserta_id')
			->where('pertemuan_id', (int) $pertemuanId)
			->get('absensi')->result();
		$mapId = array();
		foreach ($ada as $row) {
			$mapId[(int) $row->peserta_id] = (int) $row->id;
		}

		$this->db->trans_begin();

		foreach ($rows as $row) {
			$pesertaId = (int) $row['peserta_id'];
			$isi = array(
				'status' => $row['status'],
				'hal_mulai' => $row['hal_mulai'] !== NULL ? (int) $row['hal_mulai'] : NULL,
				'hal_selesai' => $row['hal_selesai'] !== NULL ? (int) $row['hal_selesai'] : NULL,
				'catatan_guru' => $row['catatan_guru'] !== '' ? $row['catatan_guru'] : NULL,
			);

			if (isset($mapId[$pesertaId])) {
				$this->db->where('id', $mapId[$pesertaId])->update('absensi', $isi);
			} else {
				$isi['pertemuan_id'] = (int) $pertemuanId;
				$isi['peserta_id'] = $pesertaId;
				$this->db->insert('absensi', $isi);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Riwayat absensi + progres milik satu peserta (semua kelasnya),
	 * terbaru di atas — satu query dengan label kelas.
	 */
	public function riwayatByPeserta($pesertaId)
	{
		return $this->db
			->select('a.*, pm.nomor AS pertemuan_nomor, pm.tanggal AS pertemuan_tanggal,
				k.id AS kelas_ui_id, k.mode,
				p.kode AS program_kode, p.nama AS program_nama,
				l.nama AS level_nama,
				c.nama AS cabang_nama, c.wilayah AS cabang_wilayah', FALSE)
			->from('absensi a')
			->join('pertemuan pm', 'pm.id = a.pertemuan_id')
			->join('kelas k', 'k.id = pm.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('a.peserta_id', (int) $pesertaId)
			->order_by('pm.tanggal', 'DESC')
			->order_by('pm.nomor', 'DESC')
			->get()
			->result();
	}

	/** Statistik kehadiran satu peserta: [total, hadir]. */
	public function statistik($pesertaId)
	{
		return $this->statistikUntuk(array((int) $pesertaId));
	}

	/** Statistik kehadiran gabungan beberapa profil peserta (dashboard akun wali). */
	public function statistikUntuk(array $pesertaIds)
	{
		if (! $pesertaIds) {
			return array('total' => 0, 'hadir' => 0);
		}
		$row = $this->db
			->select("COUNT(*) AS total, COALESCE(SUM(status = 'hadir'), 0) AS hadir", FALSE)
			->where_in('peserta_id', $pesertaIds)
			->get('absensi')
			->row();
		return array(
			'total' => (int) $row->total,
			'hadir' => (int) $row->hadir,
		);
	}
}
