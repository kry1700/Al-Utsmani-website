<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login, registrasi (akun peserta/wali), dan logout.
 */
class Auth extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('auth');
	}

	/** URL tujuan per role setelah login. */
	private function homeUrl($role)
	{
		$map = array(
			'admin' => 'admin/dashboard',
			'guru' => 'guru/dashboard',
			'peserta' => 'peserta/dashboard',
		);
		return isset($map[$role]) ? $map[$role] : 'beranda';
	}

	public function login()
	{
		if ($this->isLoggedIn()) {
			redirect($this->homeUrl($this->user['role']));
		}

		if ($this->input->method() !== 'post') {
			$this->render('auth/login', array('title' => 'Login'));
			return;
		}

		// Rate limiting: 10 percobaan login per IP per 15 menit.
		if (! rate_limit('login_'.$this->input->ip_address(), 10, 15)) {
			$this->session->set_flashdata('gagal', 'Terlalu banyak percobaan login. Coba lagi beberapa menit lagi.');
			redirect('auth/login');
			return;
		}

		$this->form_validation->set_rules('identifier', 'Email atau Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->render('auth/login', array('title' => 'Login'));
			return;
		}

		$identifier = trim((string) $this->input->post('identifier'));
		$password = (string) $this->input->post('password');
		$user = $this->Users_model->findByLogin($identifier);

		// Pesan umum agar tidak membocorkan apakah akun ada.
		$gagal = 'Email/username atau password salah.';
		if ($user && $user->status !== 'aktif') {
			$gagal = 'Akun Anda sedang nonaktif. Hubungi admin lembaga.';
		} elseif ($user && password_verify($password, $user->password)) {
			$this->session->set_userdata(array(
				'logged_in' => TRUE,
				'user_id' => (int) $user->id,
				'username' => $user->username,
				'nama' => $user->nama,
				'role' => $user->role,
			));
			redirect($this->homeUrl($user->role));
			return;
		}

		$this->session->set_flashdata('gagal', $gagal);
		redirect('auth/login');
	}

	public function registrasi()
	{
		if ($this->isLoggedIn()) {
			redirect($this->homeUrl($this->user['role']));
		}

		if ($this->input->method() !== 'post') {
			$this->render('auth/registrasi', array('title' => 'Registrasi Akun'));
			return;
		}

		// Rate limiting: 5 registrasi per IP per jam.
		if (! rate_limit('registrasi_'.$this->input->ip_address(), 5, 60)) {
			$this->session->set_flashdata('gagal', 'Terlalu banyak permintaan registrasi. Coba lagi nanti.');
			redirect('auth/registrasi');
			return;
		}

		$this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_.]+$/]|is_unique[users.username]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('no_wa', 'No. WhatsApp', 'required|trim|regex_match[/^[0-9+\- ]{6,20}$/]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
		$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password]');

		if ($this->form_validation->run() === FALSE) {
			$this->render('auth/registrasi', array('title' => 'Registrasi Akun'));
			return;
		}

		$nama = trim((string) $this->input->post('nama'));

		// Satu transaksi: akun users + profil peserta pemilik.
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
				// Profil pertama = pemilik akun (bisa berupa wali); data lain
				// (tanggal lahir, alamat, dll) dilengkapi di halaman Profil.
			)
		);

		if (! $pesertaId) {
			$this->session->set_flashdata('gagal', 'Registrasi gagal. Username atau email mungkin baru saja dipakai, coba lagi.');
			redirect('auth/registrasi');
			return;
		}

		$this->session->set_flashdata('sukses', 'Registrasi berhasil. Silakan login dengan username/email dan password Anda.');
		redirect('auth/login');
	}

	public function logout()
	{
		// Lepas data user tapi pertahankan sesi agar flash message tetap tampil
		// (sess_destroy() akan membatalkan flash yang baru ditulis).
		$this->session->unset_userdata(array('logged_in', 'user_id', 'username', 'nama', 'role'));
		$this->session->set_flashdata('sukses', 'Anda sudah keluar.');
		redirect('auth/login');
	}
}
