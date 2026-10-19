<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cabang_model extends CI_Model {

	/** Semua cabang (termasuk nonaktif) terurut wilayah + nama. */
	public function all()
	{
		return $this->db->order_by('wilayah', 'ASC')->order_by('nama', 'ASC')->get('cabang')->result();
	}

	public function aktif()
	{
		return $this->db->where('status', 'aktif')
			->order_by('wilayah', 'ASC')->order_by('nama', 'ASC')
			->get('cabang')->result();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('cabang')->row();
	}

	public function countAktif()
	{
		return (int) $this->db->where('status', 'aktif')->count_all_results('cabang');
	}

	/** Daftar wilayah unik (untuk grup/filter), terurut kemunculan pertama. */
	public function daftarWilayah()
	{
		$rows = $this->db->select('wilayah')->group_by('wilayah')
			->order_by('MIN(id)', 'ASC')->get('cabang')->result();
		$out = array();
		foreach ($rows as $row) {
			$out[] = $row->wilayah;
		}
		return $out;
	}

	/** TRUE bila nama+wilayah sudah dipakai cabang lain ($kecualiId = id saat edit). */
	public function isDuplicate($nama, $wilayah, $kecualiId = 0)
	{
		if ($kecualiId > 0) {
			$this->db->where('id !=', (int) $kecualiId);
		}
		return $this->db->where('nama', $nama)->where('wilayah', $wilayah)
			->count_all_results('cabang') > 0;
	}

	public function create($data)
	{
		$this->db->insert('cabang', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function update($id, $data)
	{
		$this->db->where('id', (int) $id)->update('cabang', $data);
		return TRUE;
	}
}
