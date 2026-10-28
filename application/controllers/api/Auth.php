<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Users_model');
		$this->load->model('Peserta_model');
		$this->load->helper('auth');
	}

	public function login()
	{
		if ($this->input->method() !== 'post') {
			$this->error('Method tidak diizinkan.', 405);
		}

		// Rate limiting
		if (! rate_limit('api_login_'.$this->input->ip_address(), 10, 15)) {
			$this->error('Terlalu banyak percobaan login. Coba lagi beberapa menit lagi.', 429);
		}

		$identifier = trim((string) $this->input->post('identifier'));
		$password = (string) $this->input->post('password');

		if ($identifier === '' || $password === '') {
			$this->error('Email/username dan password wajib diisi.', 400);
		}

		$user = $this->Users_model->findByLogin($identifier);

		if (! $user) {
			$this->error('Email/username atau password salah.', 401);
		}

		if ($user->status !== 'aktif') {
			$this->error('Akun Anda sedang nonaktif. Hubungi admin lembaga.', 403);
		}

		if (! password_verify($password, $user->password)) {
			$this->error('Email/username atau password salah.', 401);
		}

		// Generate token
		$token = bin2hex(random_bytes(32));
		$expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

		// Simpan token ke database jika tabel ada
		$this->db->insert('api_tokens', array(
			'user_id' => $user->id,
			'token' => $token,
			'device_info' => $this->input->user_agent(),
			'expires_at' => $expires_at,
		));

		$this->response(array(
			'token' => $token,
			'expires_at' => $expires_at,
			'user' => array(
				'id' => (int) $user->id,
				'nama' => $user->nama,
				'username' => $user->username,
				'email' => $user->email,
				'role' => $user->role,
			)
		), 200, 'Login berhasil');
	}

	public function register()
	{
		if ($this->input->method() !== 'post') {
			$this->error('Method tidak diizinkan.', 405);
		}

		if (! rate_limit('api_register_'.$this->input->ip_address(), 5, 60)) {
			$this->error('Terlalu banyak permintaan registrasi. Coba lagi nanti.', 429);
		}

		$nama = trim((string) $this->input->post('nama'));
		$username = trim((string) $this->input->post('username'));
		$email = trim((string) $this->input->post('email'));
		$no_wa = trim((string) $this->input->post('no_wa'));
		$password = (string) $this->input->post('password');
		$konfirmasi = (string) $this->input->post('konfirmasi_password');

		$errors = array();
		if (strlen($nama) < 3) $errors['nama'] = 'Nama minimal 3 karakter';
		if (! preg_match('/^[a-zA-Z0-9_.]+$/', $username) || strlen($username) < 3) $errors['username'] = 'Username tidak valid';
		if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email tidak valid';
		if (! preg_match('/^[0-9+\- ]{6,20}$/', $no_wa)) $errors['no_wa'] = 'No. WhatsApp tidak valid';
		if (strlen($password) < 8) $errors['password'] = 'Password minimal 8 karakter';
		if ($password !== $konfirmasi) $errors['konfirmasi_password'] = 'Konfirmasi password tidak sama';

		if ($this->Users_model->findByLogin($username) || $this->db->where('email', $email)->count_all_results('users') > 0) {
			$errors['username'] = $errors['username'] ?: 'Username atau email sudah terpakai';
			$errors['email'] = $errors['email'] ?: 'Username atau email sudah terpakai';
		}

		if (! empty($errors)) {
			$this->error('Validasi gagal', 422, $errors);
		}

		$pesertaId = $this->Peserta_model->createWithUser(
			array(
				'nama' => $nama,
				'username' => $username,
				'email' => $email,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'role' => 'peserta',
				'status' => 'aktif',
			),
			array(
				'nama' => $nama,
				'no_wa' => $no_wa,
			)
		);

		if (! $pesertaId) {
			$this->error('Registrasi gagal. Coba lagi.', 500);
		}

		$this->response(array('peserta_id' => $pesertaId), 201, 'Registrasi berhasil. Silakan login.');
	}

	public function logout()
	{
		$this->requireAuth();
		if ($this->token) {
			$this->db->where('token', $this->token)->delete('api_tokens');
		}
		$this->response(null, 200, 'Logout berhasil');
	}
}
