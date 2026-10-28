<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pembayaran_model');
		$this->load->model('Tagihan_model');
		$this->load->model('Pendaftaran_model');
		$this->load->model('Peserta_model');
		$this->load->helper('auth');
	}

	public function upload_bukti()
	{
		$this->requireAuth();
		if ($this->input->method() !== 'post') {
			$this->error('Method tidak diizinkan', 405);
		}

		$profil_list = $this->getPesertaProfiles();
		if (empty($profil_list)) {
			$this->error('Anda belum memiliki profil peserta', 400);
		}

		$jenis = $this->input->post('jenis'); // pendaftaran atau spp
		$peserta_id = (int) $this->input->post('peserta_id');
		
		// Validasi peserta
		$peserta_valid = false;
		foreach ($profil_list as $p) {
			if ((int) $p->id === $peserta_id) {
				$peserta_valid = true;
				break;
			}
		}
		if (! $peserta_valid) {
			$this->error('Profil peserta tidak valid', 403);
		}

		// Validasi file
		$err = validasi_bukti($_FILES);
		if ($err) {
			$this->error($err, 400);
		}

		$config['upload_path'] = FCPATH.'uploads/bukti_pembayaran/';
		$config['allowed_types'] = 'jpg|jpeg|png|pdf';
		$config['max_size'] = 2048;
		$config['encrypt_name'] = TRUE;

		if (! is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0755, TRUE);
		}

		$this->load->library('upload', $config);
		if (! $this->upload->do_upload('bukti')) {
			$this->error($this->upload->display_errors('', ''), 400);
		}

		$upload_data = $this->upload->data();
		$bukti_file = $upload_data['file_name'];
		$jumlah = (int) $this->input->post('jumlah');

		$data_pembayaran = array(
			'peserta_id' => $peserta_id,
			'user_id' => $this->user['id'],
			'jenis' => $jenis,
			'jumlah' => $jumlah,
			'bukti_file' => $bukti_file,
			'status' => 'menunggu',
		);

		if ($jenis === 'pendaftaran') {
			$data_pembayaran['pendaftaran_id'] = (int) $this->input->post('pendaftaran_id');
		} elseif ($jenis === 'spp') {
			$data_pembayaran['tagihan_spp_id'] = (int) $this->input->post('tagihan_spp_id');
		}

		$this->db->insert('pembayaran', $data_pembayaran);
		$pembayaran_id = $this->db->insert_id();

		$this->response(array(
			'pembayaran_id' => $pembayaran_id,
			'bukti_file' => $bukti_file,
			'status' => 'menunggu',
		), 201, 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin');
	}

	public function bukti($id)
	{
		$this->requireAuth();
		$id = (int) $id;
		
		$pembayaran = $this->db
			->where('id', $id)
			->get('pembayaran')
			->row();
		
		if (! $pembayaran) {
			show_404();
		}

		// Cek kepemilikan
		$profil_list = $this->getPesertaProfiles();
		$ada_hak = false;
		foreach ($profil_list as $p) {
			if ((int) $p->id === (int) $pembayaran->peserta_id) {
				$ada_hak = true;
				break;
			}
		}
		
		if (! $ada_hak && $this->user['role'] !== 'admin') {
			show_error('Akses ditolak', 403);
		}

		// Kirim file bukti
		$nama = basename($pembayaran->bukti_file);
		$path = FCPATH.'uploads/bukti_pembayaran/'.$nama;

		if ($nama === '' || ! is_file($path)) {
			show_404();
		}

		$mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
		$whitelist = array('image/jpeg', 'image/png', 'application/pdf');
		if (! in_array($mime, $whitelist, TRUE)) {
			$mime = 'application/octet-stream';
		}

		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$disposisi = in_array($ext, array('jpg', 'jpeg', 'png'), TRUE) ? 'inline' : 'attachment';
		$tampilan = 'buktibayaran-'.substr(md5($pembayaran->id), 0, 8).'.'.$ext;

		header('Content-Type: '.$mime);
		header('Content-Length: '.filesize($path));
		header('Content-Disposition: '.$disposisi.'; filename="'.$tampilan.'"');
		header('X-Content-Type-Options: nosniff');
		header('Cache-Control: private, no-store');

		readfile($path);
		exit;
	}
}
