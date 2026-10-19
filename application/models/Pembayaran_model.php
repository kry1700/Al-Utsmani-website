<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_model extends CI_Model {

	/**
	 * Query dasar dengan keterangan pembayaran dalam SATU query:
	 * nama peserta, label kelas (untuk jenis pendaftaran), periode SPP
	 * (untuk jenis spp), dan nama admin verifier.
	 */
	private function dasar()
	{
		return $this->db
			->select('b.*,
				ps.nama AS peserta_nama, ps.no_wa AS peserta_no_wa,
				k.id AS kelas_ui_id, k.status AS kelas_status,
				p.kode AS program_kode, p.nama AS program_nama,
				l.nama AS level_nama,
				c.id AS cabang_id, c.nama AS cabang_nama, c.wilayah AS cabang_wilayah,
				t.periode AS spp_periode, t.status AS tagihan_status, t.nominal AS tagihan_nominal,
				v.nama AS verifier_nama', FALSE)
			->from('pembayaran b')
			->join('peserta ps', 'ps.id = b.peserta_id')
			->join('pendaftaran pd', 'pd.id = b.pendaftaran_id', 'left')
			->join('kelas k', 'k.id = pd.kelas_id', 'left')
			->join('program p', 'p.id = k.program_id', 'left')
			->join('level l', 'l.id = k.level_id', 'left')
			->join('cabang c', 'c.id = k.cabang_id', 'left')
			->join('tagihan_spp t', 't.id = b.tagihan_spp_id', 'left')
			->join('users v', 'v.id = b.verified_by', 'left');
	}

	public function find($id)
	{
		return $this->dasar()->where('b.id', (int) $id)->get()->row();
	}

	/** Riwayat pembayaran milik satu akun (terbaru di atas). */
	public function byUser($userId)
	{
		return $this->dasar()
			->where('b.user_id', (int) $userId)
			->order_by('b.created_at', 'DESC')
			->get()
			->result();
	}

	/** Riwayat pembayaran milik satu peserta (admin: detail peserta). */
	public function byPeserta($pesertaId)
	{
		return $this->dasar()
			->where('b.peserta_id', (int) $pesertaId)
			->order_by('b.created_at', 'DESC')
			->get()
			->result();
	}

	/** Semua pembayaran untuk sebuah pendaftaran (urut terbaru). */
	public function byPendaftaran($pendaftaranId)
	{
		return $this->dasar()
			->where('b.pendaftaran_id', (int) $pendaftaranId)
			->order_by('b.created_at', 'DESC')
			->get()
			->result();
	}

	/** Semua pembayaran untuk sebuah tagihan SPP (urut terbaru). */
	public function byTagihan($tagihanId)
	{
		return $this->dasar()
			->where('b.tagihan_spp_id', (int) $tagihanId)
			->order_by('b.created_at', 'DESC')
			->get()
			->result();
	}

	/**
	 * Pembayaran terakhir untuk rujukan (pendaftaran/tagihan) — dipakai alur
	 * upload: belum ada → insert; ditolak/menunggu → ganti bukti (reset ke menunggu);
	 * terverifikasi → blokir.
	 */
	public function cariRef($jenis, $refId)
	{
		return $this->db
			->where('jenis', $jenis)
			->where($jenis === 'pendaftaran' ? 'pendaftaran_id' : 'tagihan_spp_id', (int) $refId)
			->order_by('id', 'DESC')
			->limit(1)
			->get('pembayaran')
			->row();
	}

	/** @return int ID pembayaran baru. */
	public function create(array $data)
	{
		$this->db->insert('pembayaran', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	/** Ganti file bukti pembayaran yang masih menunggu/ditolak → kembali menunggu. */
	public function gantiBukti($id, $namaFile)
	{
		$this->db->where('id', (int) $id)->update('pembayaran', array(
			'bukti_file' => $namaFile,
			'status' => 'menunggu',
			'catatan_admin' => NULL,
			'verified_by' => NULL,
			'verified_at' => NULL,
		));
		return TRUE;
	}

	/** Daftar untuk halaman verifikasi admin (filter status/jenis). */
	public function daftarVerifikasi($status = 'menunggu', $jenis = '')
	{
		$this->dasar();
		if ($status !== '') {
			$this->db->where('b.status', $status);
		}
		if ($jenis !== '') {
			$this->db->where('b.jenis', $jenis);
		}
		return $this->db->order_by('b.created_at', 'ASC')->get()->result();
	}

	/**
	 * Verifikasi bukti pembayaran (transaksi):
	 * pembayaran → diverifikasi, lalu status target diubah —
	 * jenis pendaftaran → pendaftaran aktif; jenis spp → tagihan lunas.
	 *
	 * @return bool TRUE bila berhasil; FALSE bila bukan berstatus menunggu
	 */
	public function verifikasi($id, $adminId)
	{
		$bayar = $this->db->where('id', (int) $id)->get('pembayaran')->row();
		if (! $bayar || $bayar->status !== 'menunggu') {
			return FALSE;
		}

		$this->db->trans_begin();

		$this->db->where('id', (int) $bayar->id)->update('pembayaran', array(
			'status' => 'diverifikasi',
			'verified_by' => (int) $adminId,
			'verified_at' => date('Y-m-d H:i:s'),
		));

		if ($bayar->jenis === 'pendaftaran') {
			$this->db->where('id', (int) $bayar->pendaftaran_id)
				->update('pendaftaran', array('status' => 'aktif'));
		} else {
			$this->db->where('id', (int) $bayar->tagihan_spp_id)->update('tagihan_spp', array(
				'status' => 'lunas',
				'lunas_at' => date('Y-m-d H:i:s'),
			));
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Tolak bukti: pendaftaran/tagihan TIDAK berubah (tetap menunggu),
	 * sehingga peserta bisa mengunggah bukti baru.
	 */
	public function tolak($id, $adminId, $catatan)
	{
		$bayar = $this->db->where('id', (int) $id)->get('pembayaran')->row();
		if (! $bayar || $bayar->status !== 'menunggu') {
			return FALSE;
		}

		$this->db->where('id', (int) $bayar->id)->update('pembayaran', array(
			'status' => 'ditolak',
			'catatan_admin' => $catatan !== '' ? $catatan : NULL,
			'verified_by' => (int) $adminId,
			'verified_at' => date('Y-m-d H:i:s'),
		));
		return TRUE;
	}
}
