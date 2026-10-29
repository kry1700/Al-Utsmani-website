<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data kelas (admin): CRUD kelas + aturan khusus:
 *   - PDHQ hanya cabang wilayah Bogor + mode offline
 *   - BILAL wajib punya tipe peserta (nama instansi opsional)
 *   - link meeting wajib & valid bila mode online
 * Status aktif/nonaktif (tanpa hard delete).
 */
class Kelas extends Admin_Controller {

	public function index()
	{
		// Filter: kosong → NULL (diabaikan Kelas_model) agar tidak memfilter
		// dengan nilai 0 yang tidak mungkin cocok.
		$ambil = function ($get, $tipe = 'int') {
			$nilai = $this->input->get($get);
			if ($nilai === NULL || $nilai === '') {
				return NULL;
			}
			if ($tipe === 'int') {
				$nilai = (int) $nilai;
				return $nilai > 0 ? $nilai : NULL;
			}
			return in_array($nilai, array('online', 'offline'), TRUE) || in_array($nilai, array('aktif', 'nonaktif'), TRUE) ? $nilai : NULL;
		};

		$filter = array(
			'program_id' => $ambil('program'),
			'level_id' => $ambil('level'),
			'cabang_id' => $ambil('cabang'),
			'mode' => $ambil('mode', 'teks'),
			'status' => $ambil('status', 'teks'),
		);

		$this->render('admin/kelas', array(
			'title' => 'Data Kelas',
			'filter' => $filter,
			'kelas' => $this->Kelas_model->daftar($filter),
			'programOptions' => $this->Program_model->options(),
			'levelOptions' => $this->Level_model->options(),
			'cabangList' => $this->Cabang_model->all(),
		));
	}

	public function tambah()
	{
		$this->render('admin/kelas_form', array(
			'title' => 'Tambah Kelas',
			'edit' => NULL,
			'programList' => $this->Program_model->all(),
			'levelList' => $this->Level_model->all(),
			'cabangList' => $this->Cabang_model->all(),
			'guruOptions' => $this->Guru_model->options(),
		));
	}

	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/kelas');
			return;
		}

		if ($this->validasi() === FALSE) {
			$this->render('admin/kelas_form', $this->formData(NULL));
			return;
		}

		$id = $this->Kelas_model->create($this->dataInput());
		if (! $id) {
			$this->session->set_flashdata('gagal', 'Kelas gagal disimpan. Coba lagi.');
			redirect('admin/kelas/tambah');
			return;
		}

		$this->session->set_flashdata('sukses', 'Kelas berhasil ditambahkan.');
		redirect('admin/kelas');
	}

	public function edit($id = 0)
	{
		$this->render('admin/kelas_form', $this->formData($this->ambil($id)));
	}

	public function update($id = 0)
	{
		$kelas = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/kelas');
			return;
		}

		if ($this->validasi() === FALSE) {
			$this->render('admin/kelas_form', $this->formData($kelas));
			return;
		}

		$this->Kelas_model->update((int) $kelas->id, $this->dataInput());

		$this->session->set_flashdata('sukses', 'Kelas berhasil diperbarui.');
		redirect('admin/kelas');
	}

	/** Aktif/nonaktifkan kelas (tidak ada hard delete). */
	public function setStatus($id = 0)
	{
		$kelas = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/kelas');
			return;
		}

		$baru = $kelas->status === 'aktif' ? 'nonaktif' : 'aktif';
		$this->Kelas_model->setStatus((int) $kelas->id, $baru);

		$this->session->set_flashdata('sukses', 'Kelas berhasil di'.$baru.'.');
		redirect('admin/kelas');
	}

	/** Data bersama untuk form (tambah/edit). */
	private function formData($edit)
	{
		return array(
			'title' => $edit === NULL ? 'Tambah Kelas' : 'Edit Kelas',
			'edit' => $edit,
			'programList' => $this->Program_model->all(),
			'levelList' => $this->Level_model->all(),
			'cabangList' => $this->Cabang_model->all(),
			'guruOptions' => $this->Guru_model->options(),
		);
	}

	/** @return object Kelas (404 bila tidak ada). */
	private function ambil($id)
	{
		$kelas = (int) $id > 0 ? $this->Kelas_model->find((int) $id) : NULL;
		if (! $kelas) {
			show_404();
			exit;
		}
		return $kelas;
	}

	/** Susun data kelas dari input (menyesuaikan program: PDHQ/BILAL). */
	private function dataInput()
	{
		$program = $this->Program_model->find((int) $this->input->post('program_id'));
		$kode = $program ? $program->kode : '';
		$mode = $this->input->post('mode') === 'online' ? 'online' : 'offline';

		$link = trim((string) $this->input->post('link_meeting'));
		$namaInstansi = trim((string) $this->input->post('nama_instansi'));

		$hari = NULL;
		$hariArr = $this->input->post('hari');
		if (is_array($hariArr)) {
			$allowed = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
			$bersih = array();
			foreach ($hariArr as $h) {
				$h = trim((string) $h);
				if ($h === '') {
					continue;
				}
				if (in_array($h, $allowed, TRUE)) {
					$bersih[] = $h;
				}
			}
			$bersih = array_values(array_unique($bersih));
			if (! empty($bersih)) {
				$hari = implode(',', $bersih);
			}
		}

		return array(
			'program_id' => (int) $this->input->post('program_id'),
			'level_id' => (int) $this->input->post('level_id'),
			'cabang_id' => (int) $this->input->post('cabang_id'),
			'guru_id' => (int) $this->input->post('guru_id'),
			'mode' => $mode,
			'hari' => $hari,
			'jadwal' => trim((string) $this->input->post('jadwal')),
			'zona_waktu' => (string) $this->input->post('zona_waktu'),
			'link_meeting' => $mode === 'online' && $link !== '' ? $link : NULL,
			'biaya_pendaftaran' => (int) $this->input->post('biaya_pendaftaran'),
			'biaya_spp' => (int) $this->input->post('biaya_spp'),
			'tipe_peserta' => $kode === 'BILAL' && $this->input->post('tipe_peserta') !== '' ? (string) $this->input->post('tipe_peserta') : NULL,
			'nama_instansi' => $kode === 'BILAL' && $namaInstansi !== '' ? $namaInstansi : NULL,
			'status' => $this->input->post('status') === 'nonaktif' ? 'nonaktif' : 'aktif',
		);
	}

	private function validasi()
	{
		$this->form_validation->set_rules('program_id', 'Program', 'required|integer');
		$this->form_validation->set_rules('level_id', 'Level', 'required|integer');
		$this->form_validation->set_rules('cabang_id', 'Cabang', 'required|integer|callback_cabangBogor');
		$this->form_validation->set_rules('guru_id', 'Guru', 'required|integer');
		$this->form_validation->set_rules('mode', 'Mode', 'required|in_list[online,offline]');
		$this->form_validation->set_rules('jadwal', 'Jadwal', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('zona_waktu', 'Zona Waktu', 'required|in_list[WIB,WITA,WIT]');
		$this->form_validation->set_rules('link_meeting', 'Link Meeting', 'trim|callback_linkMeeting');
		$this->form_validation->set_rules('biaya_pendaftaran', 'Biaya Pendaftaran', 'required|integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya SPP', 'required|integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('tipe_peserta', 'Tipe Peserta', 'trim|callback_tipePeserta');
		$this->form_validation->set_rules('nama_instansi', 'Nama Instansi', 'trim|max_length[100]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[aktif,nonaktif]');

		if ($this->form_validation->run() === FALSE) {
			return FALSE;
		}

		// Pastikan relasi (program/level/cabang/guru) benar-benar ada.
		$program = $this->Program_model->find((int) $this->input->post('program_id'));
		$level = $this->Level_model->find((int) $this->input->post('level_id'));
		$cabang = $this->Cabang_model->find((int) $this->input->post('cabang_id'));
		$guru = $this->Guru_model->find((int) $this->input->post('guru_id'));

		if (! $program || ! $level || ! $cabang || ! $guru) {
			$this->session->set_flashdata('gagal', 'Program/level/cabang/guru yang dipilih tidak valid.');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Callback aturan PDHQ: wajib cabang wilayah Bogor dan mode offline.
	 * Bila program bukan PDHQ, aturan ini dilewati (TRUE).
	 */
	public function cabangBogor($cabangId)
	{
		$program = $this->Program_model->find((int) $this->input->post('program_id'));
		if (! $program || $program->kode !== 'PDHQ') {
			return TRUE;
		}

		$cabang = $this->Cabang_model->find((int) $cabangId);
		if (! $cabang || strtolower($cabang->wilayah) !== 'bogor') {
			$this->form_validation->set_message('cabangBogor', 'Program PDHQ hanya tersedia untuk cabang wilayah Bogor.');
			return FALSE;
		}

		if ($this->input->post('mode') !== 'offline') {
			$this->form_validation->set_message('cabangBogor', 'Program PDHQ hanya tersedia dalam mode offline.');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Callback link meeting: wajib & valid (URL) bila mode online;
	 * diabaikan bila mode offline.
	 */
	public function linkMeeting($nilai)
	{
		if ($this->input->post('mode') !== 'online') {
			return TRUE;
		}

		$nilai = trim((string) $nilai);
		if ($nilai === '') {
			$this->form_validation->set_message('linkMeeting', 'Kelas online wajib memiliki link meeting.');
			return FALSE;
		}
		if (! filter_var($nilai, FILTER_VALIDATE_URL)) {
			$this->form_validation->set_message('linkMeeting', 'Link meeting harus berupa URL yang valid (mis. https://...).');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Callback tipe peserta: wajib bila program BILAL, bila tidak diabaikan.
	 */
	public function tipePeserta($nilai)
	{
		$program = $this->Program_model->find((int) $this->input->post('program_id'));
		if (! $program || $program->kode !== 'BILAL') {
			return TRUE;
		}
		if (! in_array(trim((string) $nilai), array('private', 'kelompok', 'perusahaan', 'instansi'), TRUE)) {
			$this->form_validation->set_message('tipePeserta', 'Program BILAL wajib memilih tipe peserta.');
			return FALSE;
		}
		return TRUE;
	}
}
