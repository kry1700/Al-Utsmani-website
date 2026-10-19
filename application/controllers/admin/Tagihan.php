<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SPP (admin): lihat tagihan + tunggakan, dan buat tagihan bulanan massal
 * untuk semua peserta aktif yang belum ditagih pada periode terpilih.
 */
class Tagihan extends Admin_Controller {

	public function index()
	{
		$periode = (string) $this->input->get('periode');
		if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
			$periode = periode_ini();
		}

		$status = (string) $this->input->get('status');
		if (! in_array($status, array('belum', 'lunas', 'tunggakan', ''), TRUE)) {
			$status = '';
		}

		$this->render('admin/tagihan', array(
			'title' => 'Tagihan SPP',
			'periode' => $periode,
			'status' => $status,
			'tagihan' => $this->Tagihan_model->daftar($periode, $status),
			'ringkasan' => $this->Tagihan_model->ringkasan($periode),
		));
	}

	/** Form pembuatan tagihan massal untuk satu periode. */
	public function buat()
	{
		$periode = (string) $this->input->get('periode');
		if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
			$periode = periode_ini();
		}

		$this->render('admin/tagihan_buat', array(
			'title' => 'Buat Tagihan SPP',
			'periode' => $periode,
			'jatuhTempo' => $periode.'-10', // default jatuh tempo tanggal 10
			'belum' => $this->Tagihan_model->belumDitagih($periode),
		));
	}

	/** Simpan tagihan massal (POST) dalam satu transaksi. */
	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/tagihan');
			return;
		}

		$this->form_validation->set_rules('periode', 'Periode', 'required|regex_match[/^\d{4}-\d{2}$/]');
		$this->form_validation->set_rules('jatuh_tempo', 'Jatuh Tempo', 'required|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('gagal', 'Periode atau jatuh tempo tidak valid.');
			redirect('admin/tagihan/buat');
			return;
		}

		$periode = (string) $this->input->post('periode');
		$jatuhTempo = (string) $this->input->post('jatuh_tempo');

		// Nama field datar: pilih_{peserta_id}, nominal_{peserta_id}.
		$items = array();
		foreach ($this->Tagihan_model->belumDitagih($periode) as $baris) {
			$pid = (int) $baris['peserta']->id;
			if ($this->input->post('pilih_'.$pid) === NULL) {
				continue; // tidak dicentang
			}

			$nominal = trim((string) $this->input->post('nominal_'.$pid));
			if (! ctype_digit($nominal)) {
				$this->session->set_flashdata('gagal', 'Nominal untuk '.$baris['peserta']->nama.' harus berupa angka.');
				redirect('admin/tagihan/buat?periode='.$periode);
				return;
			}

			$items[] = array('peserta_id' => $pid, 'nominal' => (int) $nominal);
		}

		if (! $items) {
			$this->session->set_flashdata('gagal', 'Tidak ada peserta yang dipilih.');
			redirect('admin/tagihan/buat?periode='.$periode);
			return;
		}

		$hasil = $this->Tagihan_model->createBatch($periode, $jatuhTempo, $items);

		if ($hasil['dibuat'] === 0) {
			$this->session->set_flashdata('gagal', 'Tidak ada tagihan baru dibuat (semua sudah ditagih pada periode ini).');
			redirect('admin/tagihan/buat?periode='.$periode);
			return;
		}

		$pesan = $hasil['dibuat'].' tagihan dibuat untuk periode '.nama_bulan($periode).'.';
		if ($hasil['dilewati'] > 0) {
			$pesan .= ' '.$hasil['dilewati'].' dilewati karena sudah ada.';
		}
		$this->session->set_flashdata('sukses', $pesan);
		redirect('admin/tagihan?periode='.$periode);
	}
}
