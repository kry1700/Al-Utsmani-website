<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pembayaran & SPP milik akun peserta/wali:
 * tagihan SPP (upload bukti) + riwayat seluruh pembayaran.
 */
class Pembayaran extends Peserta_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('auth');
		$this->load->library('upload');
	}

	public function index()
	{
		$tagihan = $this->Tagihan_model->byUser($this->user['id']);
		$riwayat = $this->Pembayaran_model->byUser($this->user['id']);

		// Peta pembayaran terakhir per tagihan (untuk status "bukti menunggu").
		$bayarPerTagihan = array();
		foreach ($riwayat as $bayar) {
			if ($bayar->jenis === 'spp' && $bayar->tagihan_spp_id !== NULL) {
				$id = (int) $bayar->tagihan_spp_id;
				if (! isset($bayarPerTagihan[$id])) {
					$bayarPerTagihan[$id] = $bayar;
				}
			}
		}

		$data = array(
			'title' => 'Pembayaran dan SPP',
			'tagihan' => $tagihan,
			'riwayat' => $riwayat,
			'bayarPerTagihan' => $bayarPerTagihan,
		);
		$this->render('peserta/pembayaran', $data);
	}

	/**
	 * Unggah / unggah ulang bukti pembayaran SPP (POST multipart).
	 * Tagihan tetap "belum" sampai admin memverifikasi.
	 */
	public function uploadTagihan($tagihanId = 0)
	{
		if ($this->input->method() !== 'post') {
			redirect('peserta/pembayaran');
			return;
		}

		// Rate limiting unggah: 20 kali per jam per akun.
		if (! rate_limit('upload_'.$this->user['id'], 20, 60)) {
			$this->session->set_flashdata('gagal', 'Terlalu banyak unggahan. Coba lagi nanti.');
			redirect('peserta/pembayaran');
			return;
		}

		$tagihan = $this->cariTagihan($tagihanId);
		if (! $tagihan) {
			return; // pesan/403 sudah ditangani cariTagihan()
		}

		$sebelumnya = $this->Pembayaran_model->cariRef('spp', (int) $tagihan->id);
		if ($sebelumnya && $sebelumnya->status === 'diverifikasi') {
			$this->session->set_flashdata('gagal', 'Tagihan SPP ini sudah lunas.');
			redirect('peserta/pembayaran');
			return;
		}

		$gagal = validasi_bukti($_FILES);
		if ($gagal !== '') {
			$this->session->set_flashdata('gagal', $gagal);
			redirect('peserta/pembayaran');
			return;
		}

		$tujuan = FCPATH.'uploads/bukti_pembayaran/';
		if (! is_dir($tujuan) || ! is_writable($tujuan)) {
			$this->session->set_flashdata('gagal', 'Folder unggahan tidak dapat ditulis. Hubungi admin.');
			redirect('peserta/pembayaran');
			return;
		}

		$config = array(
			'upload_path' => $tujuan,
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'max_size' => 2048,
			'file_name' => bin2hex(random_bytes(16)),
			'encrypt_name' => FALSE,
		);
		$this->upload->initialize($config);

		if (! $this->upload->do_upload('bukti')) {
			$this->session->set_flashdata('gagal', 'Gagal mengunggah file. Pastikan format JPG/PNG/PDF dan ukuran maksimal 2 MB.');
			redirect('peserta/pembayaran');
			return;
		}

		$info = $this->upload->data();
		$namaFile = $info['file_name'];

		if ($sebelumnya) {
			$fileLama = $sebelumnya->bukti_file;
			$this->Pembayaran_model->gantiBukti((int) $sebelumnya->id, $namaFile);
			if ($fileLama !== $namaFile && is_file($tujuan.basename($fileLama))) {
				@unlink($tujuan.basename($fileLama));
			}
		} else {
			$baru = $this->Pembayaran_model->create(array(
				'peserta_id' => (int) $tagihan->peserta_id,
				'user_id' => (int) $this->user['id'],
				'jenis' => 'spp',
				'pendaftaran_id' => NULL,
				'tagihan_spp_id' => (int) $tagihan->id,
				'jumlah' => (int) $tagihan->nominal,
				'bukti_file' => $namaFile,
				'status' => 'menunggu',
			));
			if (! $baru) {
				@unlink($tujuan.$namaFile);
				$this->session->set_flashdata('gagal', 'Bukti gagal disimpan. Coba lagi.');
				redirect('peserta/pembayaran');
				return;
			}
		}

		$this->session->set_flashdata('sukses', 'Bukti pembayaran SPP '.nama_bulan($tagihan->periode).' berhasil diunggah dan menunggu verifikasi admin.');
		redirect('peserta/pembayaran');
	}

	/** Lihat file bukti milik sendiri (admin lewat admin/pembayaran/bukti). */
	public function bukti($pembayaranId = 0)
	{
		$row = (int) $pembayaranId > 0 ? $this->Pembayaran_model->find((int) $pembayaranId) : NULL;
		if (! $row || (int) $row->user_id !== (int) $this->user['id']) {
			show_404();
			return;
		}
		$this->kirimBukti((array) $row);
	}

	/**
	 * Cari tagihan + pastikan milik akun ini; bila tidak → 403/redirect.
	 * @return object|NULL
	 */
	private function cariTagihan($tagihanId)
	{
		$tagihan = (int) $tagihanId > 0 ? $this->Tagihan_model->find((int) $tagihanId) : NULL;
		if (! $tagihan) {
			show_404();
			return NULL;
		}
		$profil = $this->Peserta_model->find((int) $tagihan->peserta_id);
		if (! $profil || (int) $profil->user_id !== (int) $this->user['id']) {
			show_error('Tagihan ini bukan milik Anda.', 403, 'Akses Ditolak');
			return NULL;
		}
		return $tagihan;
	}
}
