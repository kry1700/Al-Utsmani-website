<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gelombang_model extends CI_Model {

	private function dasar()
	{
		return $this->db
			->from('gelombang g')
			->join('program p', 'p.id = g.program_id');
	}

	public function all($filter = array())
	{
		$this->dasar()->select('g.*, p.kode AS program_kode, p.nama AS program_nama', FALSE);
		if (isset($filter['program_id']) && $filter['program_id']) {
			$this->db->where('g.program_id', (int) $filter['program_id']);
		}
		if (isset($filter['status']) && $filter['status']) {
			$this->db->where('g.status', $filter['status']);
		}
		$this->db->order_by('g.tgl_buka', 'DESC');
		$this->db->order_by('g.id', 'DESC');
		return $this->db->get()->result();
	}

	public function find($id)
	{
		return $this->dasar()
			->select('g.*, p.kode AS program_kode, p.nama AS program_nama', FALSE)
			->where('g.id', (int) $id)
			->get()
			->row();
	}

	public function aktifBisaDaftar()
	{
		$hariIni = date('Y-m-d');
		return $this->dasar()
			->select('g.*, p.kode AS program_kode, p.nama AS program_nama', FALSE)
			->where('g.status', 'buka')
			->where('g.tgl_buka <=', $hariIni)
			->where('g.tgl_tutup >=', $hariIni)
			->order_by('g.tgl_buka', 'ASC')
			->get()
			->result();
	}

	public function create(array $data)
	{
		$this->db->insert('gelombang', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('gelombang', $data);
		return TRUE;
	}

	public function setStatus($id, $status)
	{
		return $this->update($id, array('status' => $status));
	}

	public function countPendaftar($gelombangId)
	{
		return (int) $this->db->where('gelombang_id', (int) $gelombangId)->count_all_results('pendaftaran_gelombang');
	}
}
