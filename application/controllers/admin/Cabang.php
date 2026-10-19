<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data cabang (admin): CRUD nama + wilayah + status (nonaktif, bukan hapus).
 */
class Cabang extends Admin_Controller {

	public function index()
	{
		// Kelompokkan per wilayah agar tampilan rapi (tanpa query tambahan).
		$perWilayah = array();
		foreach ($this->Cabang_model->all() as $row) {
			$perWilayah[$row->wilayah][] = $row;
		}

		$this->render('admin/cabang', array(
			'title' => 'Data Cabang',
			'perWilayah' => $perWilayah,
		));
	}

	public function tambah()
	{
		$this->render('admin/cabang_form', array(
			'title' => 'Tambah Cabang',
			'edit' => NULL,
			'wilayah' => $this->Cabang_model->daftarWilayah(),
		));
	}

	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/cabang');
			return;
		}

		if ($this->validasi() === FALSE) {
			$this->render('admin/cabang_form', array(
				'title' => 'Tambah Cabang',
				'edit' => NULL,
				'wilayah' => $this->Cabang_model->daftarWilayah(),
			));
			return;
		}

		$this->Cabang_model->create(array(
			'wilayah' => trim((string) $this->input->post('wilayah')),
			'nama' => trim((string) $this->input->post('nama')),
			'status' => $this->input->post('status') === 'nonaktif' ? 'nonaktif' : 'aktif',
		));

		$this->session->set_flashdata('sukses', 'Cabang berhasil ditambahkan.');
		redirect('admin/cabang');
	}

	public function edit($id = 0)
	{
		$cabang = $this->ambil($id);

		$this->render('admin/cabang_form', array(
			'title' => 'Edit Cabang',
			'edit' => $cabang,
			'wilayah' => $this->Cabang_model->daftarWilayah(),
		));
	}

	public function update($id = 0)
	{
		$cabang = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/cabang');
			return;
		}

		if ($this->validasi((int) $cabang->id) === FALSE) {
			$this->render('admin/cabang_form', array(
				'title' => 'Edit Cabang',
				'edit' => $cabang,
				'wilayah' => $this->Cabang_model->daftarWilayah(),
			));
			return;
		}

		$this->Cabang_model->update((int) $cabang->id, array(
			'wilayah' => trim((string) $this->input->post('wilayah')),
			'nama' => trim((string) $this->input->post('nama')),
			'status' => $this->input->post('status') === 'nonaktif' ? 'nonaktif' : 'aktif',
		));

		$this->session->set_flashdata('sukses', 'Cabang berhasil diperbarui.');
		redirect('admin/cabang');
	}

	/** Aktif/nonaktifkan cabang (tidak ada hard delete). */
	public function setStatus($id = 0)
	{
		$cabang = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/cabang');
			return;
		}

		$baru = $cabang->status === 'aktif' ? 'nonaktif' : 'aktif';
		$this->Cabang_model->update((int) $cabang->id, array('status' => $baru));

		$this->session->set_flashdata('sukses', 'Cabang '.$cabang->nama.' di'.$baru.'.');
		redirect('admin/cabang');
	}

	/** @return object Cabang (404 bila tidak ada). */
	private function ambil($id)
	{
		$cabang = (int) $id > 0 ? $this->Cabang_model->find((int) $id) : NULL;
		if (! $cabang) {
			show_404();
			exit;
		}
		return $cabang;
	}

	private function validasi($kecualiId = 0)
	{
		$this->form_validation->set_rules('wilayah', 'Wilayah', 'required|trim|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('nama', 'Nama Cabang', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[aktif,nonaktif]');

		if ($this->form_validation->run() === FALSE) {
			return FALSE;
		}

		// Cek duplikat wilayah+nama (pesan jelas, bukan error constraint DB).
		$wilayah = trim((string) $this->input->post('wilayah'));
		$nama = trim((string) $this->input->post('nama'));
		if ($this->Cabang_model->isDuplicate($nama, $wilayah, $kecualiId)) {
			$this->session->set_flashdata('gagal', 'Cabang "'.$nama.'" di wilayah '.$wilayah.' sudah ada.');
			return FALSE;
		}

		return TRUE;
	}
}
