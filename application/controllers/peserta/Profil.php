<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profil & data peserta: kelola beberapa profil (mis. anak-anak) dalam satu akun.
 * Data pribadi boleh di-edit; profil tidak dihapus (riwayat belajar tetap tersimpan).
 */
class Profil extends Peserta_Controller {

	public function index()
	{
		$data = array(
			'title' => 'Profil dan Data Peserta',
			'profilList' => $this->profilList,
		);
		$this->render('peserta/profil', $data);
	}

	public function tambah()
	{
		$this->render('peserta/profil_form', array(
			'title' => 'Tambah Peserta',
			'edit' => NULL,
		));
	}

	public function edit($id = 0)
	{
		$profil = $this->profilMilik($id);
		$this->render('peserta/profil_form', array(
			'title' => 'Edit Data Peserta',
			'edit' => $profil,
		));
	}

	/** Simpan profil baru (POST). */
	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('peserta/profil');
			return;
		}
		if ($this->validasi() === FALSE) {
			$this->render('peserta/profil_form', array('title' => 'Tambah Peserta', 'edit' => NULL));
			return;
		}

		$id = $this->Peserta_model->createSaja($this->dataInput((int) $this->user['id']));
		if (! $id) {
			$this->session->set_flashdata('gagal', 'Data peserta gagal disimpan. Coba lagi.');
			redirect('peserta/profil');
			return;
		}

		$this->session->set_flashdata('sukses', 'Data peserta berhasil ditambahkan.');
		redirect('peserta/profil');
	}

	/** Perbarui profil yang sudah ada (POST). */
	public function update($id = 0)
	{
		if ($this->input->method() !== 'post') {
			redirect('peserta/profil');
			return;
		}
		$profil = $this->profilMilik($id);

		if ($this->validasi() === FALSE) {
			$this->render('peserta/profil_form', array('title' => 'Edit Data Peserta', 'edit' => $profil));
			return;
		}

		$this->Peserta_model->updateProfil((int) $profil->id, $this->dataInput((int) $this->user['id']));
		$this->session->set_flashdata('sukses', 'Data peserta berhasil diperbarui.');
		redirect('peserta/profil');
	}

	/** Rules validasi bersama untuk tambah & update. */
	private function validasi()
	{
		$this->form_validation->set_rules('nama', 'Nama Peserta', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'trim|regex_match[/^\\d{4}-\\d{2}-\\d{2}$/]');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
		$this->form_validation->set_rules('no_wa', 'No. WhatsApp', 'required|trim|regex_match[/^[0-9+\\- ]{6,20}$/]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim|max_length[100]');
		$this->form_validation->set_rules('kemampuan_awal', 'Kemampuan Awal', 'required|in_list[pemula,hafalan]');

		return $this->form_validation->run();
	}

	/** Susun data profil dari input (kolom kosong → NULL). */
	private function dataInput($userId)
	{
		$ambil = function ($field) {
			$nilai = trim((string) $this->input->post($field));
			return $nilai === '' ? NULL : $nilai;
		};

		return array(
			'user_id' => (int) $userId,
			'nama' => trim((string) $this->input->post('nama')),
			'tanggal_lahir' => $ambil('tanggal_lahir'),
			'jenis_kelamin' => (string) $this->input->post('jenis_kelamin'),
			'no_wa' => trim((string) $this->input->post('no_wa')),
			'alamat' => $ambil('alamat'),
			'nama_wali' => $ambil('nama_wali'),
			'kemampuan_awal' => (string) $this->input->post('kemampuan_awal'),
		);
	}
}
