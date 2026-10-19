<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Verifikasi pembayaran (admin): daftar bukti menunggu, setujui/tolak, dan
 * penyajian file bukti. Verifikasi mengubah status pendaftaran/tagihan
 * sekaligus (lihat Pembayaran_model::verifikasi).
 */
class Pembayaran extends Admin_Controller {

	public function index()
	{
		$status = (string) $this->input->get('status');
		if (! in_array($status, array('menunggu', 'diverifikasi', 'ditolak', ''), TRUE)) {
			$status = 'menunggu';
		}

		$jenis = (string) $this->input->get('jenis');
		if (! in_array($jenis, array('pendaftaran', 'spp', ''), TRUE)) {
			$jenis = '';
		}

		$this->render('admin/pembayaran', array(
			'title' => 'Verifikasi Pembayaran',
			'status' => $status,
			'jenis' => $jenis,
			'pembayaran' => $this->Pembayaran_model->daftarVerifikasi($status, $jenis),
		));
	}

	/** Setujui bukti → pendaftaran aktif / tagihan lunas (POST). */
	public function verifikasi($id = 0)
	{
		$bayar = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/pembayaran');
			return;
		}

		if (! $this->Pembayaran_model->verifikasi((int) $bayar->id, (int) $this->user['id'])) {
			$this->session->set_flashdata('gagal', 'Pembayaran ini sudah tidak berstatus menunggu.');
			redirect('admin/pembayaran');
			return;
		}

		$this->session->set_flashdata('sukses', 'Pembayaran '.$bayar->peserta_nama.' berhasil diverifikasi.');
		redirect('admin/pembayaran');
	}

	/** Tolak bukti dengan catatan; target tetap menunggu (boleh unggah ulang). */
	public function tolak($id = 0)
	{
		$bayar = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/pembayaran');
			return;
		}

		$this->form_validation->set_rules('catatan_admin', 'Catatan', 'required|trim|max_length[255]');
		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('gagal', 'Catatan penolakan wajib diisi (maks 255 karakter).');
			redirect('admin/pembayaran');
			return;
		}

		if (! $this->Pembayaran_model->tolak((int) $bayar->id, (int) $this->user['id'], trim((string) $this->input->post('catatan_admin')))) {
			$this->session->set_flashdata('gagal', 'Pembayaran ini sudah tidak berstatus menunggu.');
			redirect('admin/pembayaran');
			return;
		}

		$this->session->set_flashdata('sukses', 'Bukti pembayaran '.$bayar->peserta_nama.' ditolak; peserta dapat mengunggah ulang.');
		redirect('admin/pembayaran');
	}

	/** Sajikan file bukti (admin boleh melihat semua). */
	public function bukti($id = 0)
	{
		$bayar = (int) $id > 0 ? $this->Pembayaran_model->find((int) $id) : NULL;
		if (! $bayar) {
			show_404();
			return;
		}
		$this->kirimBukti((array) $bayar);
	}

	/** @return object Pembayaran (404 bila tidak ada). */
	private function ambil($id)
	{
		$bayar = (int) $id > 0 ? $this->Pembayaran_model->find((int) $id) : NULL;
		if (! $bayar) {
			show_404();
			exit;
		}
		return $bayar;
	}
}
