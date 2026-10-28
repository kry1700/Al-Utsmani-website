<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller untuk REST API (mobile app)
 */
class MY_Api_Controller extends CI_Controller {

	protected $user = NULL;
	protected $token = NULL;

	public function __construct()
	{
		parent::__construct();
		
		// Set CORS headers untuk mobile app
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
		header('Content-Type: application/json; charset=utf-8');
		
		// Handle preflight request
		if (strtolower($this->input->method()) === 'options') {
			http_response_code(200);
			exit;
		}
		
		// Parse JSON input
		$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
		if (stripos($content_type, 'application/json') !== FALSE) {
			$raw = file_get_contents('php://input');
			if ($raw) {
				$json = json_decode($raw, TRUE);
				if (is_array($json)) {
					$_POST = array_merge($_POST, $json);
					$_REQUEST = array_merge($_REQUEST, $json);
				}
			}
		}
		
		$this->load->helper('auth');
		$this->load->library('session');
		
		// Coba autentikasi via token
		$this->authenticate();
	}

	/**
	 * Autentikasi via Bearer Token
	 */
	private function authenticate()
	{
		$auth_header = $this->input->get_request_header('Authorization');
		if (! $auth_header) {
			// Coba dari $_SERVER
			if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
				$auth_header = $_SERVER['HTTP_AUTHORIZATION'];
			} elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
				$auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			}
		}

		if ($auth_header && preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
			$this->token = $matches[1];
			$this->user = $this->validateToken($this->token);
		}
	}

	/**
	 * Validasi token - simple implementation (bisa diganti dengan JWT)
	 * Untuk saat ini kita pakai session-based atau token dari database
	 */
	private function validateToken($token)
	{
		// Coba cari user dari session jika ada
		if ($this->session->userdata('logged_in')) {
			return array(
				'id' => (int) $this->session->userdata('user_id'),
				'username' => (string) $this->session->userdata('username'),
				'nama' => (string) $this->session->userdata('nama'),
				'role' => (string) $this->session->userdata('role'),
			);
		}
		
		// Token format sederhana: base64(user_id:timestamp:hash) 
		// Untuk implementasi awal, kita bisa decode dan validasi
		// Atau lebih simpel: cek di tabel api_tokens jika ada
		$this->db->where('token', $token);
		$this->db->where('expires_at >', date('Y-m-d H:i:s'));
		$token_row = $this->db->get('api_tokens')->row();
		
		if ($token_row) {
			$user = $this->Users_model->find($token_row->user_id);
			if ($user && $user->status === 'aktif') {
				return array(
					'id' => (int) $user->id,
					'username' => $user->username,
					'nama' => $user->nama,
					'role' => $user->role,
				);
			}
		}
		
		return NULL;
	}

	/**
	 * Kirim response JSON
	 */
	protected function response($data = array(), $status_code = 200, $message = '')
	{
		http_response_code($status_code);
		$response = array(
			'success' => $status_code >= 200 && $status_code < 300,
			'message' => $message,
			'data' => $data,
		);
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		exit;
	}

	/**
	 * Response error
	 */
	protected function error($message, $status_code = 400, $errors = array())
	{
		http_response_code($status_code);
		$response = array(
			'success' => false,
			'message' => $message,
			'errors' => $errors,
			'data' => null,
		);
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		exit;
	}

	/**
	 * Cek apakah sudah login
	 */
	protected function requireAuth()
	{
		if (! $this->user) {
			$this->error('Silakan login terlebih dahulu.', 401);
		}
	}

	/**
	 * Cek role
	 */
	protected function requireRole($role)
	{
		$this->requireAuth();
		if ($this->user['role'] !== $role) {
			$this->error('Akses ditolak.', 403);
		}
	}

	/**
	 * Cek apakah user adalah guru aktif
	 */
	protected function requireGuru()
	{
		$this->requireAuth();
		$guru = $this->Guru_model->byUser($this->user['id']);
		if (! $guru || $guru->status !== 'aktif') {
			$this->error('Hanya guru aktif yang dapat mengakses fitur ini.', 403);
		}
		return $guru;
	}

	/**
	 * Get data guru aktif
	 */
	protected function getGuru()
	{
		if (! $this->user) {
			return NULL;
		}
		$guru = $this->Guru_model->byUser($this->user['id']);
		if ($guru && $guru->status === 'aktif') {
			return $guru;
		}
		return NULL;
	}

	/**
	 * Cek apakah user adalah peserta
	 */
	protected function getPesertaProfiles()
	{
		if (! $this->user) {
			return array();
		}
		return $this->Peserta_model->byUser($this->user['id']);
	}
}
