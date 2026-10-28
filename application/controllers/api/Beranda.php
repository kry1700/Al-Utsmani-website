<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Program_model');
		$this->load->model('Kelas_model');
		$this->load->model('Cabang_model');
	}

	public function index()
	{
		// Data publik untuk landing page mobile
		$programs = $this->Program_model->all();
		$rekap = $this->Kelas_model->rekapProgram();
		$cabangs = $this->Cabang_model->all();

		$data = array(
			'programs' => array(),
			'cabangs' => array(),
		);

		foreach ($programs as $p) {
			$data['programs'][] = array(
				'id' => (int) $p->id,
				'kode' => $p->kode,
				'nama' => $p->nama,
				'deskripsi' => $p->deskripsi,
				'urutan' => (int) $p->urutan,
				'jumlah_kelas' => isset($rekap[$p->id]) ? (int) $rekap[$p->id]['jumlah'] : 0,
			);
		}

		foreach ($cabangs as $c) {
			if ($c->status === 'aktif') {
				$data['cabangs'][] = array(
					'id' => (int) $c->id,
					'wilayah' => $c->wilayah,
					'nama' => $c->nama,
				);
			}
		}

		$this->response($data, 200, 'Beranda berhasil diambil');
	}
}
