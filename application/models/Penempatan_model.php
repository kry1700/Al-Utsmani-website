<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Penempatan level/rank peserta oleh pengelola (admin).
 *
 * Level BUKAN pilihan peserta: pengelola menetapkan lewat proses penempatan.
 * Setiap penetapan menyimpan riwayat (tabel penempatan_level) sekaligus
 * memperbarui level terkini di tabel peserta.
 */
class Penempatan_model extends CI_Model {

	/** Riwayat penempatan satu peserta (terbaru di atas) + nama penilai. */
	public function riwayatByPeserta($pesertaId)
	{
		return $this->db
			->select('pl.*, l.nama AS level_nama, l.urutan AS level_urutan, u.nama AS penilai_nama', FALSE)
			->from('penempatan_level pl')
			->join('level l', 'l.id = pl.level_id')
			->join('users u', 'u.id = pl.penilai_user_id')
			->where('pl.peserta_id', (int) $pesertaId)
			->order_by('pl.created_at', 'DESC')
			->order_by('pl.id', 'DESC')
			->get()
			->result();
	}

	/** Riwayat penempatan terbaru seluruh peserta (untuk dashboard admin). */
	public function terbaru($limit = 5)
	{
		return $this->db
			->select('pl.*, l.nama AS level_nama, ps.nama AS peserta_nama, u.nama AS penilai_nama', FALSE)
			->from('penempatan_level pl')
			->join('level l', 'l.id = pl.level_id')
			->join('peserta ps', 'ps.id = pl.peserta_id')
			->join('users u', 'u.id = pl.penilai_user_id')
			->order_by('pl.created_at', 'DESC')
			->order_by('pl.id', 'DESC')
			->limit((int) $limit)
			->get()
			->result();
	}

	/**
	 * Tetapkan level peserta: catat riwayat + perbarui level terkini, satu transaksi.
	 *
	 * @return bool TRUE bila berhasil
	 */
	public function tetapkan($pesertaId, $levelId, $penilaiUserId, $catatan = NULL)
	{
		$this->db->trans_begin();

		$this->db->insert('penempatan_level', array(
			'peserta_id' => (int) $pesertaId,
			'level_id' => (int) $levelId,
			'penilai_user_id' => (int) $penilaiUserId,
			'catatan' => ($catatan === NULL || $catatan === '') ? NULL : $catatan,
		));

		$this->db->where('id', (int) $pesertaId)->update('peserta', array(
			'level_id' => (int) $levelId,
			'level_ditetapkan_at' => date('Y-m-d H:i:s'),
			'level_ditetapkan_by' => (int) $penilaiUserId,
		));

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}
}
