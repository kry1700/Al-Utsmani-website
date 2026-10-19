<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data guru (admin): daftar, buat akun+profil, edit, aktif/nonaktifkan.
 * Status akun dan profil guru selalu disinkronkan (satu transaksi).
 */
class Guru extends Admin_Controller {

	public function index()
	{
		$this->render('admin/guru', array(
			'title' => 'Data Guru',
			'guru' => $this->Guru_model->all(),
		));
	}

	public function tambah()
	{
		$this->render('admin/guru_form', array(
			'title' => 'Tambah Guru',
			'edit' => NULL,
			'akun' => NULL,
		));
	}

	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/guru');
			return;
		}

		if ($this->validasi(0) === FALSE) {
			$this->render('admin/guru_form', array('title' => 'Tambah Guru', 'edit' => NULL, 'akun' => NULL));
			return;
		}

		$nama = trim((string) $this->input->post('nama'));

		$guruId = $this->Guru_model->createWithUser(
			array(
				'nama' => $nama,
				'username' => trim((string) $this->input->post('username')),
				'email' => trim((string) $this->input->post('email')),
				'password' => password_hash((string) $this->input->post('password'), PASSWORD_DEFAULT),
				'role' => 'guru',
				'status' => 'aktif',
			),
			array(
				'nama' => $nama,
				'no_wa' => $this->noWa(),
			)
		);

		if (! $guruId) {
			$this->session->set_flashdata('gagal', 'Guru gagal disimpan. Username/email mungkin sudah dipakai.');
			redirect('admin/guru/tambah');
			return;
		}

		$this->session->set_flashdata('sukses', 'Guru '.$nama.' berhasil ditambahkan.');
		redirect('admin/guru');
	}

	public function edit($id = 0)
	{
		$guru = $this->ambil($id);

		$this->render('admin/guru_form', array(
			'title' => 'Edit Guru',
			'edit' => $guru,
			'akun' => $this->Users_model->find((int) $guru->user_id),
		));
	}

	public function update($id = 0)
	{
		$guru = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/guru');
			return;
		}

		if ($this->validasi((int) $guru->user_id) === FALSE) {
			$this->render('admin/guru_form', array(
				'title' => 'Edit Guru',
				'edit' => $guru,
				'akun' => $this->Users_model->find((int) $guru->user_id),
			));
			return;
		}

		$nama = trim((string) $this->input->post('nama'));
		$this->Guru_model->updateProfil((int) $guru->id, array('nama' => $nama, 'no_wa' => $this->noWa()));
		$this->Users_model->update((int) $guru->user_id, array(
			'nama' => $nama,
			'username' => trim((string) $this->input->post('username')),
			'email' => trim((string) $this->input->post('email')),
		));

		$this->session->set_flashdata('sukses', 'Data guru berhasil diperbarui.');
		redirect('admin/guru');
	}

	/** Aktif/nonaktifkan profil guru + akun loginnya sekaligus. */
	public function setStatus($id = 0)
	{
		$guru = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/guru');
			return;
		}

		$baru = $guru->status === 'aktif' ? 'nonaktif' : 'aktif';
		if (! $this->Guru_model->setStatus((int) $guru->id, $baru, (int) $guru->user_id)) {
			$this->session->set_flashdata('gagal', 'Status guru gagal diubah. Coba lagi.');
			redirect('admin/guru');
			return;
		}

		$this->session->set_flashdata('sukses', 'Guru '.$guru->nama.' di'.$baru.'.');
		redirect('admin/guru');
	}

	/** @return object Profil guru (404 bila tidak ada). */
	private function ambil($id)
	{
		$guru = (int) $id > 0 ? $this->Guru_model->find((int) $id) : NULL;
		if (! $guru) {
			show_404();
			exit;
		}
		return $guru;
	}

	/** Normalisasi no_wa (kosong → NULL). */
	private function noWa()
	{
		$nilai = trim((string) $this->input->post('no_wa'));
		return $nilai === '' ? NULL : $nilai;
	}

	/** Rules validasi (tambah: password wajib; edit: password opsional). */
	private function validasi($kecualiUserId)
	{
		$this->form_validation->set_rules('nama', 'Nama Guru', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_.]+$/]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
		$this->form_validation->set_rules('no_wa', 'No. WhatsApp', 'trim|regex_match[/^[0-9+\- ]{6,20}$/]');

		if ($kecualiUserId === 0) {
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
		} else {
			$this->form_validation->set_rules('password', 'Password', 'min_length[8]|max_length[255]');
		}

		if ($this->form_validation->run() === FALSE) {
			return FALSE;
		}

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

		if ($kecualiUserId > 0 && ($pw = (string) $this->input->post('password')) !== '') {
			$this->Users_model->update((int) $kecualiUserId, array('password' => password_hash($pw, PASSWORD_DEFAULT)));
		}

		return TRUE;
	}
}
