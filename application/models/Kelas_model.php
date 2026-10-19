<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_model extends CI_Model {

	/**
	 * Kolom dasar + label kelas dalam SATU query (dipakai semua method agar
	 * tidak ada N+1). Kolom alias dipakai helper label_kelas():
	 * program_kode, program_nama, level_nama, cabang_nama, mode.
	 */
	private function kolom()
	{
		return 'k.*,
			p.kode AS program_kode, p.nama AS program_nama,
			l.nama AS level_nama,
			c.nama AS cabang_nama, c.wilayah AS cabang_wilayah,
			g.nama AS guru_nama';
	}

	/** FROM kelas + JOIN label; filter opsional per kolom k.* (kunci tanpa prefiks). */
	private function dasar(array $filter = array())
	{
		$this->db
			->from('kelas k')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->join('guru g', 'g.id = k.guru_id');

		foreach ($filter as $kolom => $nilai) {
			if ($nilai !== NULL && $nilai !== '') {
				$this->db->where('k.'.$kolom, $nilai);
			}
		}
		return $this->db;
	}

	/** Katalog/list kelas: filter opsional, urutan tampil. */
	public function daftar(array $filter = array())
	{
		return $this->dasar($filter)
			->select($this->kolom(), FALSE)
			->order_by('c.wilayah', 'ASC')
			->order_by('c.nama', 'ASC')
			->order_by('l.urutan', 'ASC')
			->get()
			->result();
	}

	/** Detail kelas dengan label (NULL bila tidak ada). */
	public function find($id)
	{
		return $this->dasar()
			->select($this->kolom(), FALSE)
			->where('k.id', (int) $id)
			->get()
			->row();
	}

	/** Kelas yang diampu satu guru. */
	public function byGuru($guruId)
	{
		return $this->dasar(array('guru_id' => (int) $guruId))
			->select($this->kolom(), FALSE)
			->order_by('k.status', 'ASC')
			->order_by('c.nama', 'ASC')
			->get()
			->result();
	}

	/**
	 * Kelas diampu + jumlah peserta aktif tiap kelas (COUNT agregat per baris,
	 * bukan query terpisah).
	 */
	public function byGuruDenganPeserta($guruId)
	{
		return $this->dasar(array('guru_id' => (int) $guruId))
			->select($this->kolom().', COUNT(DISTINCT pd.id) AS jumlah_peserta', FALSE)
			->join('pendaftaran pd', "pd.kelas_id = k.id AND pd.status = 'aktif'", 'left', FALSE)
			->group_by('k.id')
			->order_by('k.status', 'ASC')
			->order_by('c.nama', 'ASC')
			->get()
			->result();
	}

	public function countAktif()
	{
		return (int) $this->db->where('status', 'aktif')->count_all_results('kelas');
	}

	/**
	 * Rekap kelas aktif per program (satu query) untuk carousel katalog &
	 * kartu pemilih cabang/program (beranda + /daftar).
	 *
	 * @return array [program_id => [
	 *   'jumlah' => int,
	 *   'cabang' => [cabang_id => ['nama'=>..,'wilayah'=>..], ...],
	 *   'kelas'  => [ ['kelas_id','cabang_id','cabang','wilayah','level','urutan','jadwal','zona','mode'], ... ],
	 * ]]
	 * 'kelas' dipakai kartu program untuk menampilkan level · jadwal · cabang.
	 */
	public function rekapProgram()
	{
		$rows = $this->db
			->select('k.id AS kelas_id, k.program_id, k.jadwal, k.zona_waktu, k.mode,
				c.id AS cabang_id, c.nama AS cabang_nama, c.wilayah AS cabang_wilayah,
				l.nama AS level_nama, l.urutan AS level_urutan', FALSE)
			->from('kelas k')
			->join('cabang c', 'c.id = k.cabang_id')
			->join('level l', 'l.id = k.level_id')
			->where('k.status', 'aktif')
			->order_by('l.urutan', 'ASC')
			->order_by('c.wilayah', 'ASC')
			->order_by('c.nama', 'ASC')
			->get()
			->result();

		$out = array();
		foreach ($rows as $row) {
			$pid = (int) $row->program_id;
			if (! isset($out[$pid])) {
				$out[$pid] = array('jumlah' => 0, 'cabang' => array(), 'kelas' => array());
			}
			$out[$pid]['jumlah']++;
			$cid = (int) $row->cabang_id;
			if (! isset($out[$pid]['cabang'][$cid])) {
				$out[$pid]['cabang'][$cid] = array('nama' => $row->cabang_nama, 'wilayah' => $row->cabang_wilayah);
			}
			$out[$pid]['kelas'][] = array(
				'kelas_id' => (int) $row->kelas_id,
				'cabang_id' => $cid,
				'cabang' => $row->cabang_nama,
				'wilayah' => $row->cabang_wilayah,
				'level' => $row->level_nama,
				'urutan' => (int) $row->level_urutan,
				'jadwal' => $row->jadwal,
				'zona' => $row->zona_waktu,
				'mode' => $row->mode,
			);
		}
		return $out;
	}

	public function create(array $data)
	{
		$this->db->insert('kelas', $data);
		return $this->db->insert_id() ?: FALSE;
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('kelas', $data);
		return TRUE;
	}

	public function setStatus($id, $status)
	{
		return $this->update($id, array('status' => $status));
	}
}
