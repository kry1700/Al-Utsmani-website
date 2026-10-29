<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gelombang extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('Gelombang_model', 'Pendaftaran_gelombang_model', 'Peserta_model', 'Program_model'));
		$this->load->helper(array('url', 'form'));
	}

	public function index()
	{
		$this->load->view('layouts/header', array('title' => 'Pendaftaran Gelombang'));
		$this->load->view('gelombang/index', array(
			'gelombang' => $this->Gelombang_model->aktifBisaDaftar(),
		));
		$this->load->view('layouts/footer');
	}

	public function daftar($id = 0)
	{
		if (! $this->session->userdata('logged_in')) {
			$this->session->set_flashdata('gagal', 'Silakan login terlebih dahulu untuk mendaftar minat gelombang.');
			redirect('auth/login');
			return;
		}
		$g = $this->Gelombang_model->find($id);
		if (! $g) {
			show_404();
			return;
		}
		$hariIni = date('Y-m-d');
		if ($g->status !== 'buka' || $g->tgl_buka > $hariIni || $g->tgl_tutup < $hariIni) {
			$this->session->set_flashdata('gagal', 'Pendaftaran gelombang ini tidak tersedia saat ini.');
			redirect('gelombang');
			return;
		}
		$userId = (int) $this->session->userdata('user_id');
		if ($this->Pendaftaran_gelombang_model->sudahDaftar((int) $g->id, $userId)) {
			$this->session->set_flashdata('gagal', 'Kamu sudah mendaftar minat di gelombang ini.');
			redirect('peserta/gelombang');
			return;
		}
		if ($this->input->method() === 'post') {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('nama_calon', 'Nama Calon', 'required|trim|max_length[100]');
			$this->form_validation->set_rules('no_hp', 'No. HP', 'required|trim|max_length[20]');
			if ($this->form_validation->run() === FALSE) {
				$this->load->view('layouts/header', array('title' => 'Daftar Minat - '.$g->angkatan));
				$this->load->view('gelombang/daftar', array('gelombang' => $g));
				$this->load->view('layouts/footer');
				return;
			}
			$email = trim((string) $this->input->post('email'));
			$peserta = $this->db->where('user_id', $userId)->get('peserta')->row();
			$data = array(
				'gelombang_id' => (int) $g->id,
				'user_id' => $userId,
				'peserta_id' => $peserta ? (int) $peserta->id : NULL,
				'nama_calon' => trim((string) $this->input->post('nama_calon')),
				'no_hp' => trim((string) $this->input->post('no_hp')),
				'email' => $email !== '' ? $email : $this->session->userdata('email'),
				'status' => 'menunggu',
			);
			$this->Pendaftaran_gelombang_model->create($data);
			$this->session->set_flashdata('sukses', 'Pendaftaran minat berhasil dikirim. Admin akan menghubungi kamu.');
			redirect('peserta/gelombang');
			return;
		}
		$user = $this->db->where('id', $userId)->get('users')->row();
		$peserta = $this->db->where('user_id', $userId)->get('peserta')->row();
		$this->load->view('layouts/header', array('title' => 'Daftar Minat - '.$g->angkatan));
		$this->load->view('gelombang/daftar', array('gelombang' => $g, 'user' => $user, 'peserta' => $peserta));
		$this->load->view('layouts/footer');
	}
}
