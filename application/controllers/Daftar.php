<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Alur pendaftaran terpadu: pilih cabang⇄program → pilih kelas → isi registrasi.
 *
 * - Belum punya akun: registrasi membuat AKUN + PROFIL + PENDAFTARAN sekaligus,
 *   bukti pembayaran boleh diunggah langsung (bila ada biaya pendaftaran).
 * - Sudah login sebagai peserta/wali: cukup pilih profil (anak), tanpa form akun.
 *
 * Level TIDAK dipilih peserta di sini — pengelola menetapkannya lewat proses
 * penempatan (lihat admin/penempatan).
 */
class Daftar extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('auth');
		$this->load->library('upload');
	}

	/** Langkah 1 — pemilih cabang/program (dua arah). */
	public function index()
	{
		$this->render('daftar/pilih', array(
			'title' => 'Daftar Kelas',
			'programs' => $this->Program_model->all(),
			'cabangs' => $this->Cabang_model->aktif(),
			'rekap' => $this->Kelas_model->rekapProgram(),
		));
	}

	/** Langkah 2 — daftar kelas sesuai cabang/program terpilih. */
	public function kelas()
	{
		$cabangId = (int) $this->input->get('cabang');
		$programId = (int) $this->input->get('program');
		$mode = (string) $this->input->get('mode');

		if ($cabangId <= 0 && $programId <= 0) {
			$this->session->set_flashdata('gagal', 'Pilih cabang dan/atau program terlebih dahulu.');
			redirect('daftar');
			return;
		}

		$filter = array('status' => 'aktif');
		if ($cabangId > 0) {
			$filter['cabang_id'] = $cabangId;
		}
		if ($programId > 0) {
			$filter['program_id'] = $programId;
		}
		if (in_array($mode, array('online', 'offline'), TRUE)) {
			$filter['mode'] = $mode;
		}

		$this->render('daftar/kelas', array(
			'title' => 'Pilih Kelas',
			'kelas' => $this->Kelas_model->daftar($filter),
			'cabangs' => $this->Cabang_model->aktif(),
			'programs' => $this->Program_model->all(),
			'cabangTerpilih' => $cabangId > 0 ? $this->Cabang_model->find($cabangId) : NULL,
			'programTerpilih' => $programId > 0 ? $this->Program_model->find($programId) : NULL,
			'mode' => in_array($mode, array('online', 'offline'), TRUE) ? $mode : '',
		));
	}

	/** Langkah 3 — form registrasi (akun + peserta) atau pendaftaran (peserta lama). */
	public function registrasi($kelasId = 0)
	{
		$kelas = (int) $kelasId > 0 ? $this->Kelas_model->find((int) $kelasId) : NULL;
		if (! $kelas || $kelas->status !== 'aktif') {
			show_404();
		}

		if ($this->input->method() === 'post') {
			$this->simpanRegistrasi($kelas);
			return;
		}

		$this->render('daftar/registrasi', $this->dataFormRegistrasi($kelas));
	}

	/** Data view form registrasi/pendaftaran. */
	private function dataFormRegistrasi($kelas)
	{
		$profilList = array();
		$sudahDaftar = array();

		if ($this->isLoggedIn() && $this->user['role'] === 'peserta') {
			$profilList = $this->Peserta_model->byUser($this->user['id']);
			foreach ($profilList as $profil) {
				$sudahDaftar[(int) $profil->id] = $this->Pendaftaran_model->isExists((int) $kelas->id, (int) $profil->id);
			}
		}

		return array(
			'title' => 'Registrasi Pendaftaran',
			'kelas' => $kelas,
			'profilList' => $profilList,
			'sudahDaftar' => $sudahDaftar,
		);
	}

	/** Proses POST: beda perlakuan untuk tamu vs peserta yang sudah login. */
	private function simpanRegistrasi($kelas)
	{
		if ($this->isLoggedIn()) {
			if ($this->user['role'] !== 'peserta') {
				$this->session->set_flashdata('gagal', 'Pendaftaran kelas dilakukan oleh akun peserta/wali.');
				redirect('daftar');
				return;
			}

			$this->form_validation->set_rules('peserta_id', 'Peserta', 'required|integer');
			if ($this->form_validation->run() === FALSE) {
				$this->session->set_flashdata('gagal', 'Silakan pilih peserta yang akan didaftarkan.');
				redirect('daftar/registrasi/'.$kelas->id);
				return;
			}

			$profil = $this->profilMilik((int) $this->input->post('peserta_id'));

			$adaFile = $this->adaFile();
			if ($adaFile && ! rate_limit('upload_'.$this->user['id'], 20, 60)) {
				$this->session->set_flashdata('gagal', 'Terlalu banyak unggahan. Coba lagi nanti.');
				redirect('peserta/pendaftaran');
				return;
			}

			$hasil = $this->daftarkan($kelas, (int) $profil->id, (int) $this->user['id'], $profil->nama, $adaFile);
			$this->session->set_flashdata($hasil['ok'] ? 'sukses' : 'gagal', $hasil['pesan']);
			redirect($hasil['ok'] ? 'peserta/pendaftaran' : 'daftar/registrasi/'.$kelas->id);
			return;
		}

		$this->simpanRegistrasiPublik($kelas);
	}

	/** Registrasi publik: buat akun + profil + pendaftaran dalam satu alur. */
	private function simpanRegistrasiPublik($kelas)
	{
		// Rate limiting: 5 registrasi per IP per jam (seperti registrasi akun).
		if (! rate_limit('registrasi_'.$this->input->ip_address(), 5, 60)) {
			$this->session->set_flashdata('gagal', 'Terlalu banyak permintaan registrasi. Coba lagi nanti.');
			redirect('daftar/registrasi/'.$kelas->id);
			return;
		}

		$this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_.]+$/]|is_unique[users.username]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('no_wa', 'No. WhatsApp', 'required|trim|regex_match[/^[0-9+\- ]{6,20}$/]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
		$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password]');
		$this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'trim|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'in_list[L,P]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim|max_length[100]');
		$this->form_validation->set_rules('kemampuan_awal', 'Kemampuan Awal', 'in_list[pemula,hafalan]');

		if ($this->form_validation->run() === FALSE) {
			$this->render('daftar/registrasi', $this->dataFormRegistrasi($kelas));
			return;
		}

		$nama = trim((string) $this->input->post('nama'));
		$ambil = function ($field) {
			$nilai = trim((string) $this->input->post($field));
			return $nilai === '' ? NULL : $nilai;
		};

		$pesertaId = $this->Peserta_model->createWithUser(
			array(
				'nama' => $nama,
				'username' => trim((string) $this->input->post('username')),
				'email' => trim((string) $this->input->post('email')),
				'password' => password_hash((string) $this->input->post('password'), PASSWORD_DEFAULT),
				'role' => 'peserta',
				'status' => 'aktif',
			),
			array(
				'nama' => $nama,
				'no_wa' => trim((string) $this->input->post('no_wa')),
				'tanggal_lahir' => $ambil('tanggal_lahir'),
				'jenis_kelamin' => $ambil('jenis_kelamin'),
				'alamat' => $ambil('alamat'),
				'nama_wali' => $ambil('nama_wali'),
				'kemampuan_awal' => $this->input->post('kemampuan_awal') === 'hafalan' ? 'hafalan' : 'pemula',
			)
		);

		if (! $pesertaId) {
			$this->session->set_flashdata('gagal', 'Registrasi gagal. Username atau email mungkin baru saja dipakai.');
			redirect('daftar/registrasi/'.$kelas->id);
			return;
		}

		$profil = $this->Peserta_model->find((int) $pesertaId);
		$hasil = $this->daftarkan($kelas, (int) $pesertaId, (int) $profil->user_id, $nama, $this->adaFile());

		$pesan = 'Akun berhasil dibuat. '.$hasil['pesan'];
		if ($hasil['ok']) {
			$pesan .= ' Silakan login untuk memantau status pendaftaran.';
		}
		$this->session->set_flashdata($hasil['ok'] ? 'sukses' : 'gagal', $pesan);
		redirect('auth/login');
	}

	/**
	 * Buat pendaftaran (+ unggah bukti bila ada). Dipakai kedua mode.
	 *
	 * @return array ['ok' => bool, 'pesan' => string]
	 */
	private function daftarkan($kelas, $pesertaId, $userId, $nama, $adaFile = FALSE)
	{
		if ($this->Pendaftaran_model->isExists((int) $kelas->id, (int) $pesertaId)) {
			return array('ok' => FALSE, 'pesan' => $nama.' sudah pernah mendaftar kelas ini.');
		}

		$biaya = (int) $kelas->biaya_pendaftaran;
		$pdId = $this->Pendaftaran_model->create((int) $kelas->id, (int) $pesertaId, $biaya > 0 ? 'menunggu' : 'aktif');

		if (! $pdId) {
			return array('ok' => FALSE, 'pesan' => 'Pendaftaran gagal disimpan. Coba lagi.');
		}

		if ($biaya <= 0) {
			return array('ok' => TRUE, 'pesan' => 'Pendaftaran '.$nama.' langsung aktif tanpa biaya pendaftaran. Selamat belajar!');
		}

		if ($adaFile) {
			$gagal = $this->simpanBukti($kelas, (int) $pdId, (int) $pesertaId, (int) $userId);
			if ($gagal !== '') {
				return array('ok' => TRUE, 'pesan' => 'Pendaftaran '.$nama.' dibuat, tetapi bukti gagal diunggah ('.$gagal.'). Silakan unggah ulang setelah login.');
			}
			return array('ok' => TRUE, 'pesan' => 'Pendaftaran '.$nama.' dibuat & bukti pembayaran menunggu verifikasi admin.');
		}

		return array('ok' => TRUE, 'pesan' => 'Pendaftaran '.$nama.' dibuat. Silakan unggah bukti pembayaran ('.rupiah($biaya).') di halaman Pendaftaran.');
	}

	/**
	 * Unggah bukti pendaftaran (opsional, dari form registrasi).
	 *
	 * @return string Pesan error, "" bila sukses atau tidak ada berkas
	 */
	private function simpanBukti($kelas, $pendaftaranId, $pesertaId, $userId)
	{
		if (! $this->adaFile()) {
			return '';
		}

		$gagal = validasi_bukti($_FILES);
		if ($gagal !== '') {
			return $gagal;
		}

		$tujuan = FCPATH.'uploads/bukti_pembayaran/';
		if (! is_dir($tujuan) || ! is_writable($tujuan)) {
			return 'Folder unggahan tidak dapat ditulis. Hubungi admin.';
		}

		$this->upload->initialize(array(
			'upload_path' => $tujuan,
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'max_size' => 2048,
			'file_name' => bin2hex(random_bytes(16)),
			'encrypt_name' => FALSE,
		));

		if (! $this->upload->do_upload('bukti')) {
			return 'Format harus JPG/PNG/PDF maksimal 2 MB';
		}

		$info = $this->upload->data();
		$baru = $this->Pembayaran_model->create(array(
			'peserta_id' => (int) $pesertaId,
			'user_id' => (int) $userId,
			'jenis' => 'pendaftaran',
			'pendaftaran_id' => (int) $pendaftaranId,
			'tagihan_spp_id' => NULL,
			'jumlah' => (int) $kelas->biaya_pendaftaran,
			'bukti_file' => $info['file_name'],
			'status' => 'menunggu',
		));

		if (! $baru) {
			@unlink($tujuan.$info['file_name']);
			return 'Bukti gagal disimpan';
		}

		return '';
	}

	/** @return bool TRUE bila request memuat berkas bukti. */
	private function adaFile()
	{
		return isset($_FILES['bukti']) && is_array($_FILES['bukti'])
			&& isset($_FILES['bukti']['error'])
			&& (int) $_FILES['bukti']['error'] !== UPLOAD_ERR_NO_FILE;
	}

	/** Pastikan profil peserta milik akun login (403 bila bukan). */
	private function profilMilik($pesertaId)
	{
		$profil = (int) $pesertaId > 0 ? $this->Peserta_model->find((int) $pesertaId) : NULL;
		if (! $profil || (int) $profil->user_id !== (int) $this->user['id']) {
			show_error('Data peserta tidak ditemukan atau bukan milik Anda.', 403, 'Akses Ditolak');
			exit;
		}
		return $profil;
	}
}
