<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

	/** Cari user berdasarkan username ATAU email (untuk login satu field). */
	public function findByLogin($nilai)
	{
		return $this->db
			->group_start()
				->where('username', $nilai)
				->or_where('email', $nilai)
			->group_end()
			->get('users')
			->row();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('users')->row();
	}

	/** @return int ID user baru, atau FALSE bila gagal. */
	public function create($data)
	{
		$this->db->insert('users', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function update($id, $data)
	{
		$this->db->where('id', (int) $id)->update('users', $data);
		return TRUE;
	}

	/**
	 * TRUE bila username/email sudah terpakai (kecuali milik $kecualiId).
	 * Dipanggil tanpa argumen saat cek registrasi (email & username dicek terpisah).
	 */
	public function isTaken($username = '', $email = '', $kecualiId = 0)
	{
		if ($username === '' && $email === '') {
			return FALSE;
		}
		if ($kecualiId > 0) {
			$this->db->where('id !=', (int) $kecualiId);
		}
		$this->db->group_start();
		if ($username !== '') {
			$this->db->or_where('username', $username);
		}
		if ($email !== '') {
			$this->db->or_where('email', $email);
		}
		$this->db->group_end();
		return $this->db->count_all_results('users') > 0;
	}
}
