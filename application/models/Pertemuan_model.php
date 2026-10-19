<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertemuan_model extends CI_Model {

	/** Daftar pertemuan sebuah kelas (terurut nomor). */
	public function byKelas($kelasId)
	{
		return $this->db
			->where('kelas_id', (int) $kelasId)
			->order_by('nomor', 'ASC')
			->get('pertemuan')
			->result();
	}

	/**
	 * Daftar pertemuan + jumlah baris absensi yang sudah diisi guru
	 * (agregat, bukan query per pertemuan).
	 */
	public function byKelasWithCounts($kelasId)
	{
		return $this->db
			->select('pm.*, COUNT(a.id) AS terisi', FALSE)
			->from('pertemuan pm')
			->join('absensi a', 'a.pertemuan_id = pm.id', 'left')
			->where('pm.kelas_id', (int) $kelasId)
			->group_by('pm.id')
			->order_by('pm.nomor', 'ASC')
			->get()
			->result();
	}

	/**
	 * Detail pertemuan + info kelas (untuk guard kepemilikan: k.guru_id).
	 */
	public function find($id)
	{
		return $this->db
			->select('pm.*, k.guru_id, k.status AS kelas_status, k.mode, k.jadwal, k.zona_waktu,
				k.link_meeting,
				p.kode AS program_kode, p.nama AS program_nama,
				l.nama AS level_nama,
				c.nama AS cabang_nama, c.wilayah AS cabang_wilayah', FALSE)
			->from('pertemuan pm')
			->join('kelas k', 'k.id = pm.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('pm.id', (int) $id)
			->get()
			->row();
	}

	/** Jumlah pertemuan seluruh kelas yang diampu seorang guru (satu query). */
	public function jumlahByGuru($guruId)
	{
		$row = $this->db
			->select('COUNT(*) AS jumlah', FALSE)
			->from('pertemuan pm')
			->join('kelas k', 'k.id = pm.kelas_id')
			->where('k.guru_id', (int) $guruId)
			->get()
			->row();
		return (int) $row->jumlah;
	}

	/** @return int Nomor berikutnya untuk kelas (1 bila belum ada pertemuan). */
	public function nextNomor($kelasId)
	{
		$row = $this->db->select_max('nomor')
			->where('kelas_id', (int) $kelasId)
			->get('pertemuan')
			->row();
		return ((int) $row->nomor) + 1;
	}

	public function isExists($kelasId, $nomor)
	{
		return $this->db
			->where('kelas_id', (int) $kelasId)
			->where('nomor', (int) $nomor)
			->count_all_results('pertemuan') > 0;
	}

	/** @return int ID pertemuan baru, atau FALSE bila nomor duplikat. */
	public function create($kelasId, $nomor, $tanggal)
	{
		if ($this->isExists($kelasId, $nomor)) {
			return FALSE;
		}
		$this->db->insert('pertemuan', array(
			'kelas_id' => (int) $kelasId,
			'nomor' => (int) $nomor,
			'tanggal' => $tanggal,
		));
		return $this->db->insert_id() ?: FALSE;
	}

	/** Baris absensi (untuk prefill form) + nama peserta. */
	public function isian($pertemuanId)
	{
		return $this->db
			->select('a.*, ps.nama AS peserta_nama', FALSE)
			->from('absensi a')
			->join('peserta ps', 'ps.id = a.peserta_id')
			->where('a.pertemuan_id', (int) $pertemuanId)
			->order_by('ps.nama', 'ASC')
			->get()
			->result();
	}
}
