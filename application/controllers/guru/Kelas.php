<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelas yang diampu guru: daftar, detail (peserta + pertemuan), dan
 * penambahan pertemuan. Semua akses dibatasi pada kelas milik guru login.
 */
class Kelas extends Guru_Controller {

	public function index()
	{
		$this->render('guru/kelas', array(
			'title' => 'Kelas Saya',
			'kelas' => $this->Kelas_model->byGuruDenganPeserta((int) $this->guru->id),
		));
	}

	public function detail($id = 0)
	{
		$kelas = $this->kelasMilik($id);

		$this->render('guru/kelas_detail', array(
			'title' => 'Detail Kelas',
			'kelas' => $kelas,
			'peserta' => $this->Pendaftaran_model->pesertaByKelas((int) $kelas->id, 'aktif'),
			'pertemuan' => $this->Pertemuan_model->byKelasWithCounts((int) $kelas->id),
			'nomorBerikut' => $this->Pertemuan_model->nextNomor((int) $kelas->id),
		));
	}

	/** Tambah pertemuan baru untuk sebuah kelas (POST). */
	public function tambahPertemuan($id = 0)
	{
		$kelas = $this->kelasMilik($id);

		if ($this->input->method() !== 'post') {
			redirect('guru/kelas/detail/'.$kelas->id);
			return;
		}

		$nomorBerikut = $this->Pertemuan_model->nextNomor((int) $kelas->id);
		$this->form_validation->set_rules('nomor', 'Nomor Pertemuan', 'required|integer|greater_than[0]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('gagal', validation_errors(' ', ' '));
			redirect('guru/kelas/detail/'.$kelas->id);
			return;
		}

		$nomor = (int) $this->input->post('nomor');
		$tanggal = (string) $this->input->post('tanggal');

		if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
			$this->session->set_flashdata('gagal', 'Format tanggal tidak valid.');
			redirect('guru/kelas/detail/'.$kelas->id);
			return;
		}

		$idBaru = $this->Pertemuan_model->create((int) $kelas->id, $nomor, $tanggal);
		if (! $idBaru) {
			$this->session->set_flashdata('gagal', 'Pertemuan nomor '.$nomor.' sudah ada. Nomor berikutnya: '.$nomorBerikut.'.');
			redirect('guru/kelas/detail/'.$kelas->id);
			return;
		}

		$this->session->set_flashdata('sukses', 'Pertemuan '.$nomor.' berhasil ditambahkan.');
		redirect('guru/absensi/isi/'.$idBaru);
	}

	/**
	 * Ambil kelas + pastikan diampu guru login; selain itu 403.
	 *
	 * @return object
	 */
	private function kelasMilik($id)
	{
		$kelas = (int) $id > 0 ? $this->Kelas_model->find((int) $id) : NULL;
		if (! $kelas || (int) $kelas->guru_id !== (int) $this->guru->id) {
			show_error('Kelas tidak ditemukan atau bukan kelas yang Anda ampu.', 403, 'Akses Ditolak');
			exit;
		}
		return $kelas;
	}
}
