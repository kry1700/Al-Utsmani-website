<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Katalog extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Program_model');
		$this->load->model('Kelas_model');
		$this->load->model('Cabang_model');
	}

	public function program()
	{
		$programs = $this->Program_model->all();
		$rekap = $this->Kelas_model->rekapProgram();

		$data = array();
		foreach ($programs as $p) {
			$data[] = array(
				'id' => (int) $p->id,
				'kode' => $p->kode,
				'nama' => $p->nama,
				'deskripsi' => $p->deskripsi,
				'urutan' => (int) $p->urutan,
				'jumlah_kelas' => isset($rekap[$p->id]) ? (int) $rekap[$p->id]['jumlah'] : 0,
				'cabang' => isset($rekap[$p->id]) ? $rekap[$p->id]['cabang'] : array(),
			);
		}

		$this->response($data, 200, 'Program berhasil diambil');
	}

	public function detail($program_id)
	{
		$program_id = (int) $program_id;
		$program = $this->Program_model->find($program_id);
		if (! $program) {
			$this->error('Program tidak ditemukan', 404);
		}

		$rekap = $this->Kelas_model->rekapProgram();
		$kelas_list = isset($rekap[$program_id]) ? $rekap[$program_id]['kelas'] : array();

		$data = array(
			'program' => array(
				'id' => (int) $program->id,
				'kode' => $program->kode,
				'nama' => $program->nama,
				'deskripsi' => $program->deskripsi,
			),
			'kelas' => array(),
		);

		foreach ($kelas_list as $k) {
			if ($k->status !== 'aktif') {
				continue;
			}
			$data['kelas'][] = array(
				'id' => (int) $k->id,
				'program_kode' => $k->program_kode,
				'program_nama' => $k->program_nama,
				'level_nama' => $k->level_nama,
				'level_urutan' => isset($k->level_urutan) ? (int) $k->level_urutan : 0,
				'cabang_nama' => $k->cabang_nama,
				'cabang_wilayah' => $k->cabang_wilayah,
				'mode' => $k->mode,
				'jadwal' => $k->jadwal,
				'zona_waktu' => $k->zona_waktu,
				'biaya_pendaftaran' => (int) $k->biaya_pendaftaran,
				'biaya_spp' => (int) $k->biaya_spp,
				'guru_nama' => isset($k->guru_nama) ? $k->guru_nama : null,
			);
		}

		$this->response($data, 200, 'Detail program berhasil diambil');
	}

	public function kelas_detail($kelas_id)
	{
		$kelas_id = (int) $kelas_id;
		$kelas = $this->Kelas_model->find($kelas_id);
		if (! $kelas) {
			$this->error('Kelas tidak ditemukan', 404);
		}

		// Hitung jumlah peserta aktif
		$jumlah_peserta = $this->db
			->where('kelas_id', $kelas_id)
			->where('status', 'aktif')
			->count_all_results('pendaftaran');

		$this->response(array(
			'id' => (int) $kelas->id,
			'program' => array(
				'kode' => isset($kelas->program_kode) ? $kelas->program_kode : null,
				'nama' => isset($kelas->program_nama) ? $kelas->program_nama : null,
			),
			'level' => array(
				'nama' => isset($kelas->level_nama) ? $kelas->level_nama : null,
			),
			'cabang' => array(
				'nama' => isset($kelas->cabang_nama) ? $kelas->cabang_nama : null,
				'wilayah' => isset($kelas->cabang_wilayah) ? $kelas->cabang_wilayah : null,
			),
			'guru' => array(
				'nama' => isset($kelas->guru_nama) ? $kelas->guru_nama : null,
			),
			'mode' => $kelas->mode,
			'jadwal' => $kelas->jadwal,
			'zona_waktu' => $kelas->zona_waktu,
			'link_meeting' => $kelas->link_meeting,
			'biaya_pendaftaran' => (int) $kelas->biaya_pendaftaran,
			'biaya_spp' => (int) $kelas->biaya_spp,
			'tipe_peserta' => $kelas->tipe_peserta,
			'nama_instansi' => $kelas->nama_instansi,
			'status' => $kelas->status,
			'jumlah_peserta_aktif' => $jumlah_peserta,
		), 200, 'Detail kelas berhasil diambil');
	}
}
