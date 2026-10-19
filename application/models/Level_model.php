<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Level kelas di-seed lewat database.sql — tanpa CRUD. */
class Level_model extends CI_Model {

	/** @return array Objek level terurut urutan (Idad → Tahfidz). */
	public function all()
	{
		return $this->db->order_by('urutan', 'ASC')->get('level')->result();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('level')->row();
	}

	/** Assoc [id => nama] untuk dropdown. */
	public function options()
	{
		$out = array();
		foreach ($this->all() as $row) {
			$out[(int) $row->id] = $row->nama;
		}
		return $out;
	}
}
