<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gelombang extends Peserta_Controller {

	public function index()
	{
		$userId = (int) $this->session->userdata('user_id');
		$this->render('peserta/gelombang/index', array(
			'title' => 'Pendaftaran Gelombang Saya',
			'daftar' => $this->Pendaftaran_gelombang_model->daftarOlehUser($userId),
		));
	}
}
