<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller aplikasi.
 *
 * CATATAN CI3: hanya file core/MY_Controller.php yang di-require otomatis
 * oleh framework, sehingga seluruh kelas base controller didefinisikan di file ini.
 */
class MY_Controller extends CI_Controller {

	/** @var array|null Data user login (id, username, nama, role) atau NULL bila tamu. */
	protected $user = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->user = $this->currentUser();
	}

	/**
	 * Render halaman: header (berisi navbar) + view + footer.
	 * Flash message & data user otomatis disuntikkan ke view.
	 */
	protected function render($view, $data = array())
	{
		$data['user'] = $this->user;
		$data['flash_sukses'] = $this->session->flashdata('sukses');
		$data['flash_gagal'] = $this->session->flashdata('gagal');

		$this->load->view('layouts/header', $data);
		$this->load->view($view, $data);
		$this->load->view('layouts/footer', $data);
	}

	/** Ambil data user dari session (NULL bila belum login). */
	private function currentUser()
	{
		if (! $this->session->userdata('logged_in')) {
			return NULL;
		}
		return array(
			'id' => (int) $this->session->userdata('user_id'),
			'username' => (string) $this->session->userdata('username'),
			'nama' => (string) $this->session->userdata('nama'),
			'role' => (string) $this->session->userdata('role'),
		);
	}

	/** @return bool TRUE bila sudah login. */
	protected function isLoggedIn()
	{
		return $this->user !== NULL;
	}

	/** Putar balik ke halaman login bila belum login. */
	protected function requireLogin()
	{
		if (! $this->isLoggedIn()) {
			$this->session->set_flashdata('gagal', 'Silakan login terlebih dahulu.');
			redirect('auth/login');
			exit;
		}
	}

	/** Wajib login dengan role tertentu; role lain mendapat 403. */
	protected function requireRole($role)
	{
		$this->requireLogin();
		if ($this->user['role'] !== $role) {
			show_error('Akses ditolak: Anda tidak memiliki hak untuk membuka halaman ini.', 403, 'Akses Ditolak');
			exit;
		}
	}

	/**
	 * Sajikan file bukti pembayaran dari penyimpanan tersembunyi.
	 * Pemanggil WAJIB sudah memeriksa hak akses (pemilik/admin).
	 *
	 * @param array $pembayaran Baris pembayaran (kolom bukti_file, jenis, id)
	 */
	protected function kirimBukti(array $pembayaran)
	{
		$nama = basename($pembayaran['bukti_file']); // pertahanan: tanpa path
		$path = FCPATH.'uploads/bukti_pembayaran/'.$nama;

		if ($nama === '' || ! is_file($path) || ! is_readable($path)) {
			show_404();
		}

		$mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
		$whitelist = array('image/jpeg', 'image/png', 'application/pdf');
		if (! in_array($mime, $whitelist, TRUE)) {
			$mime = 'application/octet-stream';
		}

		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$disposisi = in_array($ext, array('jpg', 'jpeg', 'png'), TRUE) ? 'inline' : 'attachment';

		// Nama tampil aman (hanya karakter sederhana; nama file memang acak).
		$tampilan = 'buktibayaran-'.substr(md5($pembayaran['id']), 0, 8).'.'.$ext;

		header('Content-Type: '.$mime);
		header('Content-Length: '.filesize($path));
		header('Content-Disposition: '.$disposisi.'; filename="'.$tampilan.'"');
		header('X-Content-Type-Options: nosniff');
		header('Cache-Control: private, no-store');

		readfile($path);
		exit;
	}
}

/**
 * Guard untuk halaman admin.
 */
class Admin_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->requireRole('admin');
	}
}

/**
 * Guard untuk halaman guru. $this->guru = profil guru milik akun login.
 */
class Guru_Controller extends MY_Controller {

	/** @var object|null Profil guru dari akun login. */
	protected $guru = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->requireRole('guru');

		$this->guru = $this->Guru_model->byUser($this->user['id']);
		if (! $this->guru) {
			show_error('Profil guru tidak ditemukan untuk akun ini.', 403, 'Data Tidak Ditemukan');
			exit;
		}
	}
}

/**
 * Guard untuk halaman peserta/wali. $this->profilList = daftar profil peserta
 * (bisa lebih dari satu bila akun dipakai wali untuk beberapa anak).
 */
class Peserta_Controller extends MY_Controller {

	/** @var array Daftar profil peserta milik akun login. */
	protected $profilList = array();

	public function __construct()
	{
		parent::__construct();
		$this->requireRole('peserta');

		$this->profilList = $this->Peserta_model->byUser($this->user['id']);
	}

	/**
	 * Pastikan profil peserta dimiliki akun login; selain itu 403.
	 *
	 * @return object Profil peserta
	 */
	protected function profilMilik($pesertaId)
	{
		$pesertaId = (int) $pesertaId;
		$row = $pesertaId > 0 ? $this->Peserta_model->find($pesertaId) : NULL;

		if (! $row || (int) $row->user_id !== (int) $this->user['id']) {
			show_error('Data peserta tidak ditemukan atau bukan milik Anda.', 403, 'Akses Ditolak');
			exit;
		}
		return $row;
	}
}
