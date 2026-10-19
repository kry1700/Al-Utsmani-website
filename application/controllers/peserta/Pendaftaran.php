<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pendaftaran kelas oleh peserta/wali + unggah bukti pembayaran pendaftaran.
 */
class Pendaftaran extends Peserta_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('auth');
		$this->load->library('upload');
	}

	/** Daftar pendaftaran milik akun ini + status bukti terakhir tiap baris. */
	public function index()
	{
		$pendaftaran = $this->Pendaftaran_model->byUser($this->user['id']);

		// Peta pembayaran terakhir per pendaftaran (satu query, bukan per baris).
		$bayarTerakhir = array();
		foreach ($this->Pembayaran_model->byUser($this->user['id']) as $bayar) {
			if ($bayar->jenis === 'pendaftaran' && $bayar->pendaftaran_id !== NULL) {
				// byUser urut terbaru → ambil yang pertama kali ditemui.
				$id = (int) $bayar->pendaftaran_id;
				if (! isset($bayarTerakhir[$id])) {
					$bayarTerakhir[$id] = $bayar;
				}
			}
		}

		$data = array(
			'title' => 'Pendaftaran Kelas',
			'pendaftaran' => $pendaftaran,
			'bayarTerakhir' => $bayarTerakhir,
		);
		$this->render('peserta/pendaftaran', $data);
	}

	/**
	 * Simpan pendaftaran baru (POST dari detail kelas).
	 * Biaya 0 → langsung aktif; selain itu menunggu unggah bukti + verifikasi.
	 */
	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('katalog');
			return;
		}

		$this->form_validation->set_rules('kelas_id', 'Kelas', 'required|integer');
		$this->form_validation->set_rules('peserta_id', 'Peserta', 'required|integer');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('gagal', 'Pendaftaran gagal diproses: data tidak lengkap.');
			redirect('katalog');
			return;
		}

		$kelasId = (int) $this->input->post('kelas_id');
		$kelas = $this->Kelas_model->find($kelasId);
		if (! $kelas || $kelas->status !== 'aktif') {
			$this->session->set_flashdata('gagal', 'Kelas tidak ditemukan atau sudah tidak aktif.');
			redirect('katalog');
			return;
		}

		// Profil peserta wajib milik akun ini (guard 403 bila bukan milik sendiri).
		$profil = $this->profilMilik((int) $this->input->post('peserta_id'));

		if ($this->Pendaftaran_model->isExists($kelasId, (int) $profil->id)) {
			$this->session->set_flashdata('gagal', $profil->nama.' sudah pernah mendaftar kelas ini.');
			redirect('katalog/detail/'.$kelasId);
			return;
		}

		$biaya = (int) $kelas->biaya_pendaftaran;
		$status = $biaya > 0 ? 'menunggu' : 'aktif';

		$id = $this->Pendaftaran_model->create($kelasId, (int) $profil->id, $status);
		if (! $id) {
			$this->session->set_flashdata('gagal', 'Pendaftaran gagal disimpan. Coba lagi.');
			redirect('katalog/detail/'.$kelasId);
			return;
		}

		if ($biaya > 0) {
			$this->session->set_flashdata('sukses',
				'Pendaftaran dibuat untuk '.$profil->nama.'. Silakan unggah bukti pembayaran pendaftaran ('.rupiah($biaya).').');
		} else {
			$this->session->set_flashdata('sukses',
				'Pendaftaran '.$profil->nama.' langsung aktif (tanpa biaya pendaftaran). Selamat belajar!');
		}
		redirect('peserta/pendaftaran');
	}

	/**
	 * Unggah / unggah ulang bukti pembayaran pendaftaran (POST multipart).
	 * Bukti ditolak admin → boleh unggah ulang (mengganti file yang sama).
	 */
	public function upload($pendaftaranId = 0)
	{
		if ($this->input->method() !== 'post') {
			redirect('peserta/pendaftaran');
			return;
		}

		// Rate limiting unggah: 20 kali per jam per akun.
		if (! rate_limit('upload_'.$this->user['id'], 20, 60)) {
			$this->session->set_flashdata('gagal', 'Terlalu banyak unggahan. Coba lagi nanti.');
			redirect('peserta/pendaftaran');
			return;
		}

		$pd = (int) $pendaftaranId > 0 ? $this->Pendaftaran_model->find((int) $pendaftaranId) : NULL;
		$kelas = $pd ? $this->Kelas_model->find((int) $pd->kelas_id) : NULL;
		$profil = $pd ? $this->profilMilik((int) $pd->peserta_id) : NULL;

		if (! $pd || ! $kelas || ! $profil) {
			show_error('Pendaftaran tidak ditemukan atau bukan milik Anda.', 403, 'Akses Ditolak');
			return;
		}
		if ($pd->status !== 'menunggu' || (int) $kelas->biaya_pendaftaran <= 0) {
			$this->session->set_flashdata('gagal', 'Pendaftaran ini tidak memerlukan unggahan bukti.');
			redirect('peserta/pendaftaran');
			return;
		}

		$sebelumnya = $this->Pembayaran_model->cariRef('pendaftaran', (int) $pd->id);
		if ($sebelumnya && $sebelumnya->status === 'diverifikasi') {
			$this->session->set_flashdata('gagal', 'Bukti untuk pendaftaran ini sudah terverifikasi.');
			redirect('peserta/pendaftaran');
			return;
		}

		// Validasi server-side sebelum Upload library (pesan error Bahasa Indonesia).
		$gagal = validasi_bukti($_FILES);
		if ($gagal !== '') {
			$this->session->set_flashdata('gagal', $gagal);
			redirect('peserta/pendaftaran');
			return;
		}

		$tujuan = FCPATH.'uploads/bukti_pembayaran/';
		if (! is_dir($tujuan) || ! is_writable($tujuan)) {
			$this->session->set_flashdata('gagal', 'Folder unggahan tidak dapat ditulis. Hubungi admin.');
			redirect('peserta/pendaftaran');
			return;
		}

		$config = array(
			'upload_path' => $tujuan,
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'max_size' => 2048, // KB — maksimal 2 MB
			'file_name' => bin2hex(random_bytes(16)), // nama acak, tanpa jejak nama asli
			'encrypt_name' => FALSE,
		);
		$this->upload->initialize($config);

		if (! $this->upload->do_upload('bukti')) {
			$this->session->set_flashdata('gagal', 'Gagal mengunggah file. Pastikan format JPG/PNG/PDF dan ukuran maksimal 2 MB.');
			redirect('peserta/pendaftaran');
			return;
		}

		$info = $this->upload->data();
		$namaFile = $info['file_name'];

		if ($sebelumnya) {
			$fileLama = $sebelumnya->bukti_file;
			$this->Pembayaran_model->gantiBukti((int) $sebelumnya->id, $namaFile);
			// Bersihkan file lama yang sudah diganti.
			if ($fileLama !== $namaFile && is_file($tujuan.basename($fileLama))) {
				@unlink($tujuan.basename($fileLama));
			}
		} else {
			$baru = $this->Pembayaran_model->create(array(
				'peserta_id' => (int) $pd->peserta_id,
				'user_id' => (int) $this->user['id'],
				'jenis' => 'pendaftaran',
				'pendaftaran_id' => (int) $pd->id,
				'tagihan_spp_id' => NULL,
				'jumlah' => (int) $kelas->biaya_pendaftaran,
				'bukti_file' => $namaFile,
				'status' => 'menunggu',
			));
			if (! $baru) {
				@unlink($tujuan.$namaFile);
				$this->session->set_flashdata('gagal', 'Bukti gagal disimpan. Coba lagi.');
				redirect('peserta/pendaftaran');
				return;
			}
		}

		$this->session->set_flashdata('sukses', 'Bukti pembayaran untuk pendaftaran '.$profil->nama.' berhasil diunggah dan menunggu verifikasi admin.');
		redirect('peserta/pendaftaran');
	}
}
