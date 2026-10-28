<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Me extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Guru_model');
		$this->load->model('Peserta_model');
		$this->load->model('Users_model');
	}

	public function index()
	{
		$this->requireAuth();

		$user = $this->Users_model->find($this->user['id']);
		if (! $user) {
			$this->error('User tidak ditemukan', 404);
		}

		// Cek apakah terdaftar sebagai guru
		$guru = $this->Guru_model->byUser($user->id);
		$is_guru = false;
		$guru_data = null;
		if ($guru && $guru->status === 'aktif') {
			$is_guru = true;
			$guru_data = array(
				'id' => (int) $guru->id,
				'nama' => $guru->nama,
				'no_wa' => $guru->no_wa,
				'status' => $guru->status,
			);
		}

		// Profil peserta (bisa banyak)
		$profil_peserta = array();
		$peserta_list = $this->Peserta_model->byUser($user->id);
		foreach ($peserta_list as $p) {
			$profil_peserta[] = array(
				'id' => (int) $p->id,
				'nama' => $p->nama,
				'tanggal_lahir' => $p->tanggal_lahir,
				'jenis_kelamin' => $p->jenis_kelamin,
				'level_id' => $p->level_id ? (int) $p->level_id : null,
				'level_nama' => isset($p->level_nama) ? $p->level_nama : null,
			);
		}

		$this->response(array(
			'user' => array(
				'id' => (int) $user->id,
				'nama' => $user->nama,
				'username' => $user->username,
				'email' => $user->email,
				'role' => $user->role,
				'status' => $user->status,
			),
			'is_guru' => $is_guru,
			'guru' => $guru_data,
			'profil_peserta' => $profil_peserta,
		), 200, 'Data user berhasil diambil');
	}
}
