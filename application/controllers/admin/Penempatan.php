<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Penempatan level/rank peserta (admin).
 *
 * Level BUKAN pilihan peserta: pengelola (admin) menetapkan lewat halaman ini
 * setelah melihat kemampuan awal peserta. Setiap penetapan tercatat sebagai
 * riwayat (tabel penempatan_level) dan muncul sebagai title di profil peserta.
 */
class Penempatan extends Admin_Controller {

	public function index()
	{
		$q = trim((string) $this->input->get('q'));
		$level = (string) $this->input->get('level'); // '' = semua, 'belum', atau id level

		if ($level !== '' && $level !== 'belum' && ! preg_match('/^\d+$/', $level)) {
			$level = '';
		}

		$this->render('admin/penempatan', array(
			'title' => 'Penempatan Level',
			'q' => $q,
			'levelFilter' => $level,
			'levelList' => $this->Level_model->all(),
			'peserta' => $this->Peserta_model->daftar($q, $level),
			'jumlahBelum' => $this->Peserta_model->countBelumLevel(),
		));
	}

	/** Form penetapan level + riwayat penempatan peserta. */
	public function form($id = 0)
	{
		$peserta = $this->ambil($id);

		$this->render('admin/penempatan_form', array(
			'title' => 'Penempatan Level',
			'peserta' => $peserta,
			'levelList' => $this->Level_model->all(),
			'riwayat' => $this->Penempatan_model->riwayatByPeserta((int) $peserta->id),
		));
	}

	/** Simpan penetapan level (riwayat + level terkini, satu transaksi). */
	public function simpan($id = 0)
	{
		$peserta = $this->ambil($id);

		if ($this->input->method() !== 'post') {
			redirect('admin/penempatan');
			return;
		}

		$this->form_validation->set_rules('level_id', 'Level', 'required|integer');
		$this->form_validation->set_rules('catatan', 'Catatan', 'trim|max_length[255]');

		$levelId = (int) $this->input->post('level_id');
		$valid = $this->form_validation->run() !== FALSE;
		$level = $valid ? $this->Level_model->find($levelId) : NULL;

		if (! $valid || ! $level) {
			if ($valid) {
				$this->session->set_flashdata('gagal', 'Level yang dipilih tidak dikenal.');
			}
			$this->render('admin/penempatan_form', array(
				'title' => 'Penempatan Level',
				'peserta' => $peserta,
				'levelList' => $this->Level_model->all(),
				'riwayat' => $this->Penempatan_model->riwayatByPeserta((int) $peserta->id),
			));
			return;
		}

		if (! $this->Penempatan_model->tetapkan((int) $peserta->id, $levelId, (int) $this->user['id'], (string) $this->input->post('catatan'))) {
			$this->session->set_flashdata('gagal', 'Level gagal ditetapkan. Silakan coba lagi.');
			redirect('admin/penempatan/form/'.$peserta->id);
			return;
		}

		$this->session->set_flashdata('sukses', 'Level '.$level->nama.' ditetapkan untuk '.$peserta->nama.'.');
		redirect('admin/penempatan/form/'.$peserta->id);
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
}
