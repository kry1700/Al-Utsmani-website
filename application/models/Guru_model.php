<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_model extends CI_Model {

	/** Profil guru dari sebuah akun users. */
	public function byUser($userId)
	{
		return $this->db->where('user_id', (int) $userId)->get('guru')->row();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('guru')->row();
	}

	/**
	 * Daftar guru untuk admin, satu query (join users + agregat kelas aktif).
	 */
	public function all()
	{
		return $this->db
			->select("g.*, u.username, u.email, u.status AS user_status,
				COUNT(DISTINCT CASE WHEN k.status = 'aktif' THEN k.id END) AS jumlah_kelas_aktif", FALSE)
			->from('guru g')
			->join('users u', 'u.id = g.user_id')
			->join('kelas k', 'k.guru_id = g.id', 'left')
			->group_by('g.id')
			->order_by('g.nama', 'ASC')
			->get()
			->result();
	}

	/** Daftar guru untuk dropdown kelas: [id => "Nama (nonaktif)"]. */
	public function options()
	{
		$out = array();
		foreach ($this->db->order_by('nama', 'ASC')->get('guru')->result() as $row) {
			$label = $row->nama;
			if ($row->status !== 'aktif') {
				$label .= ' (nonaktif)';
			}
			$out[(int) $row->id] = $label;
		}
		return $out;
	}

	public function countAktif()
	{
		return (int) $this->db->where('status', 'aktif')->count_all_results('guru');
	}

	/**
	 * Buat akun users + profil guru dalam satu transaksi.
	 *
	 * @return int ID profil guru baru, atau FALSE
	 */
	public function createWithUser(array $user, array $guru)
	{
		$this->db->trans_begin();

		$userId = $this->Users_model->create($user);
		if (! $userId) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$guru['user_id'] = $userId;
		$this->db->insert('guru', $guru);
		$id = $this->db->insert_id();

		if ($this->db->trans_status() === FALSE || ! $id) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return $id;
	}

	public function updateProfil($id, $data)
	{
		$this->db->where('id', (int) $id)->update('guru', $data);
		return TRUE;
	}

	/**
	 * Nonaktifkan guru: profil guru & akun loginnya ikut dinal aktif bersamaan
	 * agar status tidak berlawanan.
	 */
	public function setStatus($id, $status, $userId)
	{
		$this->db->trans_begin();

		$this->db->where('id', (int) $id)->update('guru', array('status' => $status));
		$this->db->where('id', (int) $userId)->update('users', array('status' => $status));

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();
		return TRUE;
	}
}
