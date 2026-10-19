<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data peserta (admin): daftar, detail (riwayat pendaftaran + pembayaran),
 * buat akun+profil, edit, dan aktif/nonaktifkan akun.
 * Profil peserta tidak dihapus — hanya akun yang dinonaktifkan.
 */
class Peserta extends Admin_Controller {

	public function index()
	{
		$q = trim((string) $this->input->get('q'));

		$this->render('admin/peserta', array(
			'title' => 'Data Peserta',
			'q' => $q,
			'peserta' => $this->Peserta_model->daftar($q),
		));
	}

	public function detail($id = 0)
	{
		$peserta = $this->ambil($id);

		$this->render('admin/peserta_detail', array(
			'title' => 'Detail Peserta',
			'peserta' => $peserta,
			'akun' => $this->Users_model->find((int) $peserta->user_id),
			'pendaftaran' => $this->Pendaftaran_model->byPeserta((int) $peserta->id),
			'pembayaran' => $this->Pembayaran_model->byPeserta((int) $peserta->id),
			'keluarga' => $this->Peserta_model->byUser((int) $peserta->user_id),
			'tagihan' => $this->Tagihan_model->byPeserta((int) $peserta->id),
			'riwayatLevel' => $this->Penempatan_model->riwayatByPeserta((int) $peserta->id),
		));
	}

	public function tambah()
	{
		$this->render('admin/peserta_form', array(
			'title' => 'Tambah Peserta',
			'edit' => NULL,
			'akun' => NULL,
		));
	}

	/** Simpan akun + profil peserta baru (transaksi). */
	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/peserta');
			return;
		}

		if ($this->validasi(0) === FALSE) {
			$this->render('admin/peserta_form', array('title' => 'Tambah Peserta', 'edit' => NULL, 'akun' => NULL));
			return;
		}

		$nama = trim((string) $this->input->post('nama'));

		$pesertaId = $this->Peserta_model->createWithUser(
			array(
				'nama' => $nama,
				'username' => trim((string) $this->input->post('username')),
				'email' => trim((string) $this->input->post('email')),
				'password' => password_hash((string) $this->input->post('password'), PASSWORD_DEFAULT),
				'role' => 'peserta',
				'status' => 'aktif',
			),
			$this->dataProfil()
		);

		if (! $pesertaId) {
			$this->session->set_flashdata('gagal', 'Peserta gagal disimpan. Username/email mungkin sudah dipakai.');
			redirect('admin/peserta/tambah');
			return;
		}

		$this->session->set_flashdata('sukses', 'Peserta '.$nama.' berhasil ditambahkan.');
		redirect('admin/peserta/detail/'.$pesertaId);
	}

	public function edit($id = 0)
	{
		$peserta = $this->ambil($id);

		$this->render('admin/peserta_form', array(
			'title' => 'Edit Peserta',
			'edit' => $peserta,
			'akun' => $this->Users_model->find((int) $peserta->user_id),
		));
	}

	/** Perbarui profil peserta + username/email akun (opsional). */
	public function update($id = 0)
	{
		$peserta = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/peserta/detail/'.$peserta->id);
			return;
		}

		if ($this->validasi((int) $peserta->user_id) === FALSE) {
			$this->render('admin/peserta_form', array(
				'title' => 'Edit Peserta',
				'edit' => $peserta,
				'akun' => $this->Users_model->find((int) $peserta->user_id),
			));
			return;
		}

		$this->Peserta_model->updateProfil((int) $peserta->id, $this->dataProfil());
		$this->Users_model->update((int) $peserta->user_id, array(
			'username' => trim((string) $this->input->post('username')),
			'email' => trim((string) $this->input->post('email')),
		));

		$this->session->set_flashdata('sukses', 'Data peserta berhasil diperbarui.');
		redirect('admin/peserta/detail/'.$peserta->id);
	}

	/** Aktif/nonaktifkan akun pemilik profil (data riwayat tetap utuh). */
	public function setStatus($id = 0)
	{
		$peserta = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/peserta/detail/'.$peserta->id);
			return;
		}

		$akun = $this->Users_model->find((int) $peserta->user_id);
		if (! $akun) {
			show_404();
			return;
		}

		$baru = $akun->status === 'aktif' ? 'nonaktif' : 'aktif';
		$this->Users_model->update((int) $akun->id, array('status' => $baru));

		$this->session->set_flashdata('sukses', 'Akun peserta berhasil di'.$baru.'.');
		redirect('admin/peserta/detail/'.$peserta->id);
	}

	/** @return object Peserta (404 bila tidak ada). */
	private function ambil($id)
	{
		$peserta = (int) $id > 0 ? $this->Peserta_model->find((int) $id) : NULL;
		if (! $peserta) {
			show_404();
			exit;
		}
		return $peserta;
	}

	/** Data profil dari input. */
	private function dataProfil()
	{
		$ambil = function ($field) {
			$nilai = trim((string) $this->input->post($field));
			return $nilai === '' ? NULL : $nilai;
		};

		return array(
			'nama' => trim((string) $this->input->post('nama')),
			'tanggal_lahir' => $ambil('tanggal_lahir'),
			'jenis_kelamin' => $ambil('jenis_kelamin'),
			'no_wa' => $ambil('no_wa'),
			'alamat' => $ambil('alamat'),
			'nama_wali' => $ambil('nama_wali'),
			'kemampuan_awal' => $this->input->post('kemampuan_awal') === 'hafalan' ? 'hafalan' : 'pemula',
		);
	}

	/** Rules validasi bersama (tambah: password wajib; edit: password opsional). */
	private function validasi($kecualiUserId)
	{
		$this->form_validation->set_rules('nama', 'Nama Peserta', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_.]+$/]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
		$this->form_validation->set_rules('no_wa', 'No. WhatsApp', 'trim|regex_match[/^[0-9+\- ]{6,20}$/]');
		$this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'trim|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'in_list[L,P]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim|max_length[100]');
		$this->form_validation->set_rules('kemampuan_awal', 'Kemampuan Awal', 'required|in_list[pemula,hafalan]');

		if ($kecualiUserId === 0) {
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
		} else {
			$this->form_validation->set_rules('password', 'Password', 'min_length[8]|max_length[255]');
		}

		if ($this->form_validation->run() === FALSE) {
			return FALSE;
		}

		// Cek keunikan username/email (pesan bahasa Indonesia, bukan error DB).
		$username = trim((string) $this->input->post('username'));
		$email = trim((string) $this->input->post('email'));
		if ($this->Users_model->isTaken($username, '', $kecualiUserId)) {
			$this->session->set_flashdata('gagal', 'Username "'.$username.'" sudah dipakai.');
			return FALSE;
		}
		if ($this->Users_model->isTaken('', $email, $kecualiUserId)) {
			$this->session->set_flashdata('gagal', 'Email "'.$email.'" sudah dipakai.');
			return FALSE;
		}

		// Ganti password bila diisi (mode edit).
		if ($kecualiUserId > 0 && ($pw = (string) $this->input->post('password')) !== '') {
			$this->Users_model->update((int) $kecualiUserId, array('password' => password_hash($pw, PASSWORD_DEFAULT)));
		}

		return TRUE;
	}
}
