<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta extends MY_Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Peserta_model');
		$this->load->model('Pendaftaran_model');
		$this->load->model('Tagihan_model');
		$this->load->model('Pembayaran_model');
		$this->load->model('Kelas_model');
		$this->load->helper('auth');
	}

	public function dashboard()
	{
		$this->requireAuth();
		$profil_list = $this->getPesertaProfiles();
		
		if (empty($profil_list)) {
			$this->response(array(
				'profil_peserta' => array(),
				'pendaftaran' => array(),
				'tagihan' => array(),
			), 200, 'Dashboard peserta');
		}

		$peserta_ids = array();
		foreach ($profil_list as $p) {
			$peserta_ids[] = (int) $p->id;
		}

		// Pendaftaran aktif
		$this->db->select('pd.*, k.jadwal, k.mode, k.link_meeting, p.nama AS program_nama, p.kode AS program_kode, l.nama AS level_nama, c.nama AS cabang_nama')
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where_in('pd.peserta_id', $peserta_ids)
			->where('pd.status', 'aktif');
		$pendaftaran = $this->db->get()->result();

		// Tagihan belum lunas
		$this->db->select('t.*')
			->from('tagihan_spp t')
			->where_in('t.peserta_id', $peserta_ids)
			->where('t.status', 'belum')
			->order_by('t.jatuh_tempo', 'ASC');
		$tagihan = $this->db->get()->result();

		$this->response(array(
			'profil_peserta' => $profil_list,
			'pendaftaran' => $pendaftaran,
			'tagihan' => $tagihan,
			'jumlah_tagihan_belum_lunas' => count($tagihan),
		), 200, 'Dashboard peserta berhasil diambil');
	}

	public function tagihan()
	{
		$this->requireAuth();
		$profil_list = $this->getPesertaProfiles();
		$peserta_ids = array();
		foreach ($profil_list as $p) {
			$peserta_ids[] = (int) $p->id;
		}

		if (empty($peserta_ids)) {
			$this->response(array(), 200, 'Tagihan berhasil diambil');
		}

		$this->db->select('t.*, ps.nama AS peserta_nama')
			->from('tagihan_spp t')
			->join('peserta ps', 'ps.id = t.peserta_id')
			->where_in('t.peserta_id', $peserta_ids)
			->order_by('t.periode', 'DESC')
			->order_by('t.id', 'DESC');
		$tagihan = $this->db->get()->result();

		$this->response($tagihan, 200, 'Tagihan berhasil diambil');
	}

	public function daftar_kelas()
	{
		$this->requireAuth();
		if ($this->input->method() !== 'post') {
			$this->error('Method tidak diizinkan', 405);
		}

		$profil_list = $this->getPesertaProfiles();
		if (empty($profil_list)) {
			$this->error('Anda belum memiliki profil peserta', 400);
		}

		$kelas_id = (int) $this->input->post('kelas_id');
		$peserta_id = (int) $this->input->post('peserta_id');

		// Pastikan peserta milik user
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

		$kelas = $this->Kelas_model->find($kelas_id);
		if (! $kelas) {
			$this->error('Kelas tidak ditemukan', 404);
		}
		if ($kelas->status !== 'aktif') {
			$this->error('Kelas tidak aktif', 400);
		}

		// Cek sudah terdaftar
		$sudah = $this->db
			->where('kelas_id', $kelas_id)
			->where('peserta_id', $peserta_id)
			->count_all_results('pendaftaran');
		if ($sudah > 0) {
			$this->error('Anda sudah terdaftar di kelas ini', 400);
		}

		// Buat pendaftaran
		$status = ((int) $kelas->biaya_pendaftaran == 0) ? 'aktif' : 'menunggu';
		$this->db->insert('pendaftaran', array(
			'kelas_id' => $kelas_id,
			'peserta_id' => $peserta_id,
			'status' => $status,
		));
		$pendaftaran_id = $this->db->insert_id();

		$this->response(array(
			'pendaftaran_id' => $pendaftaran_id,
			'status' => $status,
			'perlu_bayar_pendaftaran' => ((int) $kelas->biaya_pendaftaran > 0),
			'biaya_pendaftaran' => (int) $kelas->biaya_pendaftaran,
		), 201, $status === 'aktif' ? 'Pendaftaran berhasil (gratis)' : 'Pendaftaran berhasil, menunggu pembayaran');
	}
}
