<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta_model extends CI_Model {

	/** Kolom profil + nama level (join level → satu query, tanpa N+1). */
	private function kolom()
	{
		return 'ps.*, l.nama AS level_nama, l.urutan AS level_urutan';
	}

	/** FROM peserta + JOIN level (left: peserta baru belum punya level). */
	private function dasar()
	{
		return $this->db
			->from('peserta ps')
			->join('level l', 'l.id = ps.level_id', 'left');
	}

	/** Semua profil peserta milik sebuah akun (bisa banyak utk akun wali). */
	public function byUser($userId)
	{
		return $this->dasar()
			->select($this->kolom(), FALSE)
			->where('ps.user_id', (int) $userId)
			->order_by('ps.nama', 'ASC')
			->get()
			->result();
	}

	public function find($id)
	{
		return $this->dasar()
			->select($this->kolom(), FALSE)
			->where('ps.id', (int) $id)
			->get()
			->row();
	}

	/**
	 * Daftar peserta untuk admin (satu query, tanpa N+1):
	 * join akun + agregat jumlah pendaftaran aktif.
	 *
	 * @param string     $q           Pencarian nama/no_wa (kosong = semua)
	 * @param string|int $filterLevel '' = semua, 'belum' = belum ditempatkan,
	 *                                int = level_id tertentu
	 */
	public function daftar($q = '', $filterLevel = '')
	{
		$this->dasar()
			->select($this->kolom().', u.username, u.email, u.status AS user_status,
				COUNT(DISTINCT CASE WHEN pd.status = \'aktif\' THEN pd.id END) AS pendaftaran_aktif', FALSE)
			->join('users u', 'u.id = ps.user_id')
			->join('pendaftaran pd', 'pd.peserta_id = ps.id', 'left');

		if ($q !== '') {
			$this->db->group_start()
				->like('ps.nama', $q)
				->or_like('ps.no_wa', $q)
				->group_end();
		}

		if ($filterLevel === 'belum') {
			$this->db->where('ps.level_id IS NULL', NULL, FALSE);
		} elseif ((int) $filterLevel > 0) {
			$this->db->where('ps.level_id', (int) $filterLevel);
		}

		return $this->db->group_by('ps.id')->order_by('ps.nama', 'ASC')->get()->result();
	}

	/** @return int Jumlah peserta yang levelnya belum ditempatkan. */
	public function countBelumLevel()
	{
		return (int) $this->db->where('level_id IS NULL', NULL, FALSE)->count_all_results('peserta');
	}

	/**
	 * Buat akun users + profil peserta dalam satu transaksi (dipakai registrasi
	 * dan pembuatan peserta oleh admin).
	 *
	 * @return int ID profil peserta baru, atau FALSE
	 */
	public function createWithUser(array $user, array $peserta)
	{
		$this->db->trans_begin();

		$userId = $this->Users_model->create($user);
		if (! $userId) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$peserta['user_id'] = $userId;
		$this->db->insert('peserta', $peserta);
		$id = $this->db->insert_id();

		if ($this->db->trans_status() === FALSE || ! $id) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return $id;
	}

	/** Tambah profil peserta baru pada akun yang sudah ada (tanpa buat user). */
	public function createSaja(array $data)
	{
		$this->db->insert('peserta', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function updateProfil($id, $data)
	{
		$this->db->where('id', (int) $id)->update('peserta', $data);
		return TRUE;
	}
}
