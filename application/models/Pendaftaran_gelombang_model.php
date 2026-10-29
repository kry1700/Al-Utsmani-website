<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendaftaran_gelombang_model extends CI_Model {

	public function daftarPerGelombang($gelombangId, $filter = array())
	{
		$this->db
			->select('pg.*, u.username, u.email AS user_email, p.nama AS peserta_nama, p.level_id, k.id AS kelas_id_ref')
			->from('pendaftaran_gelombang pg')
			->join('users u', 'u.id = pg.user_id', 'left')
			->join('peserta p', 'p.id = pg.peserta_id', 'left')
			->join('kelas k', 'k.id = pg.kelas_id', 'left')
			->where('pg.gelombang_id', (int) $gelombangId);
		if (isset($filter['status']) && $filter['status']) {
			$this->db->where('pg.status', $filter['status']);
		}
		$this->db->order_by('pg.created_at', 'ASC');
		return $this->db->get()->result();
	}

	public function find($id)
	{
		return $this->db
			->select('pg.*, g.program_id, g.angkatan, p.kode AS program_kode, p.nama AS program_nama')
			->from('pendaftaran_gelombang pg')
			->join('gelombang g', 'g.id = pg.gelombang_id')
			->join('program p', 'p.id = g.program_id')
			->where('pg.id', (int) $id)
			->get()
			->row();
	}

	public function sudahDaftar($gelombangId, $userId)
	{
		return $this->db
			->where('gelombang_id', (int) $gelombangId)
			->where('user_id', (int) $userId)
			->get('pendaftaran_gelombang')
			->row();
	}

	public function daftarOlehUser($userId)
	{
		return $this->db
			->select('pg.*, g.angkatan, g.tgl_buka, g.tgl_tutup, g.status AS gelombang_status, p.kode AS program_kode, p.nama AS program_nama, k.id AS kelas_id_tujuan')
			->from('pendaftaran_gelombang pg')
			->join('gelombang g', 'g.id = pg.gelombang_id')
			->join('program p', 'p.id = g.program_id')
			->join('kelas k', 'k.id = pg.kelas_id', 'left')
			->where('pg.user_id', (int) $userId)
			->order_by('pg.created_at', 'DESC')
			->get()
			->result();
	}

	public function create(array $data)
	{
		$this->db->insert('pendaftaran_gelombang', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('pendaftaran_gelombang', $data);
		return TRUE;
	}
}
