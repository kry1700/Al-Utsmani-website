<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Program di-seed lewat database.sql — tanpa CRUD. */
class Program_model extends CI_Model {

	/** @return array Objek program terurut urutan. */
	public function all()
	{
		return $this->db->order_by('urutan', 'ASC')->get('program')->result();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('program')->row();
	}

	public function byKode($kode)
	{
		return $this->db->where('kode', $kode)->get('program')->row();
	}

	/** Assoc [id => "KODE — Nama"] untuk dropdown. */
	public function options()
	{
		$out = array();
		foreach ($this->all() as $row) {
			$out[(int) $row->id] = $row->kode.' — '.$row->nama;
		}
		return $out;
	}
}
