<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gelombang extends Admin_Controller {

	public function index()
	{
		$this->render('admin/gelombang/index', array(
			'title' => 'Gelombang Pendaftaran',
			'gelombang' => $this->Gelombang_model->all(),
		));
	}

	public function tambah()
	{
		$this->render('admin/gelombang/form', array(
			'title' => 'Tambah Gelombang',
			'edit' => NULL,
			'programList' => $this->Program_model->all(),
		));
	}

	public function simpan()
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/gelombang');
			return;
		}
		if ($this->validasi() === FALSE) {
			$this->render('admin/gelombang/form', array(
				'title' => 'Tambah Gelombang',
				'edit' => NULL,
				'programList' => $this->Program_model->all(),
			));
			return;
		}
		$data = $this->dataInput();
		$id = $this->Gelombang_model->create($data);
		if (! $id) {
			$this->session->set_flashdata('gagal', 'Gelombang gagal disimpan.');
			redirect('admin/gelombang/tambah');
			return;
		}
		$this->session->set_flashdata('sukses', 'Gelombang berhasil ditambahkan.');
		redirect('admin/gelombang');
	}

	public function edit($id = 0)
	{
		$g = $this->ambil($id);
		$this->render('admin/gelombang/form', array(
			'title' => 'Edit Gelombang',
			'edit' => $g,
			'programList' => $this->Program_model->all(),
		));
	}

	public function update($id = 0)
	{
		$g = $this->ambil($id);
		if ($this->input->method() !== 'post') {
			redirect('admin/gelombang');
			return;
		}
		if ($this->validasi() === FALSE) {
			$this->render('admin/gelombang/form', array(
				'title' => 'Edit Gelombang',
				'edit' => $g,
				'programList' => $this->Program_model->all(),
			));
			return;
		}
		$this->Gelombang_model->update((int) $g->id, $this->dataInput());
		$this->session->set_flashdata('sukses', 'Gelombang berhasil diperbarui.');
		redirect('admin/gelombang');
	}

	public function calon($id = 0)
	{
		$g = $this->ambil($id);
		$filter = array();
		$status = $this->input->get('status');
		if (in_array($status, array('menunggu','dihubungi','diterima_ke_kelas','ditolak'), TRUE)) {
			$filter['status'] = $status;
		}
		$this->render('admin/gelombang/calon', array(
			'title' => 'Calon Pendaftar - '.$g->angkatan,
			'gelombang' => $g,
			'calon' => $this->Pendaftaran_gelombang_model->daftarPerGelombang($g->id, $filter),
			'kelasOptions' => $this->db->select('k.id, p.kode AS program_kode, l.nama AS level_nama, c.nama AS cabang_nama, k.jadwal, k.zona_waktu')
				->from('kelas k')
				->join('program p', 'p.id = k.program_id')
				->join('level l', 'l.id = k.level_id')
				->join('cabang c', 'c.id = k.cabang_id')
				->where('k.status', 'aktif')
				->order_by('p.kode', 'ASC')
				->order_by('c.nama', 'ASC')
				->get()->result(),
		));
	}

	public function proses($id = 0)
	{
		if ($this->input->method() !== 'post') {
			redirect('admin/gelombang');
			return;
		}
		$pg = $this->Pendaftaran_gelombang_model->find($id);
		if (! $pg) {
			show_404();
			return;
		}
		$aksi = $this->input->post('aksi');
		$catatan = trim((string) $this->input->post('catatan_admin'));
		if ($aksi === 'dihubungi') {
			$this->Pendaftaran_gelombang_model->update((int) $pg->id, array(
				'status' => 'dihubungi',
				'catatan_admin' => $catatan !== '' ? $catatan : NULL,
				'diproses_by' => (int) $this->session->userdata('user_id'),
				'diproses_at' => date('Y-m-d H:i:s'),
			));
			$this->session->set_flashdata('sukses', 'Status calon diperbarui.');
			redirect('admin/gelombang/calon/'.$pg->gelombang_id);
			return;
		}
		if ($aksi === 'tolak') {
			$this->Pendaftaran_gelombang_model->update((int) $pg->id, array(
				'status' => 'ditolak',
				'catatan_admin' => $catatan !== '' ? $catatan : NULL,
				'diproses_by' => (int) $this->session->userdata('user_id'),
				'diproses_at' => date('Y-m-d H:i:s'),
			));
			$this->session->set_flashdata('sukses', 'Calon ditolak.');
			redirect('admin/gelombang/calon/'.$pg->gelombang_id);
			return;
		}
		if ($aksi === 'terima') {
			$kelasId = (int) $this->input->post('kelas_id');
			if ($kelasId < 1) {
				$this->session->set_flashdata('gagal', 'Pilih kelas tujuan terlebih dahulu.');
				redirect('admin/gelombang/calon/'.$pg->gelombang_id);
				return;
			}
			$kelas = $this->Kelas_model->find($kelasId);
			if (! $kelas) {
				$this->session->set_flashdata('gagal', 'Kelas tidak ditemukan.');
				redirect('admin/gelombang/calon/'.$pg->gelombang_id);
				return;
			}
			$pesertaId = $pg->peserta_id ? (int) $pg->peserta_id : NULL;
			if (! $pesertaId) {
				$userId = (int) $pg->user_id;
				$peserta = $this->db->where('user_id', $userId)->get('peserta')->row();
				if ($peserta) {
					$pesertaId = (int) $peserta->id;
				}
			}
			if (! $pesertaId) {
				$this->session->set_flashdata('gagal', 'Data peserta belum ditemukan untuk akun ini.');
				redirect('admin/gelombang/calon/'.$pg->gelombang_id);
				return;
			}
			$cekPendaftaran = $this->db->where('kelas_id', $kelasId)->where('peserta_id', $pesertaId)->get('pendaftaran')->row();
			if ($cekPendaftaran) {
				$this->session->set_flashdata('gagal', 'Peserta sudah terdaftar di kelas tujuan.');
				redirect('admin/gelombang/calon/'.$pg->gelombang_id);
				return;
			}
			$biaya = (int) $kelas->biaya_pendaftaran;
			$statusPendaftaran = $biaya > 0 ? 'menunggu' : 'aktif';
			$this->db->insert('pendaftaran', array(
				'kelas_id' => $kelasId,
				'peserta_id' => $pesertaId,
				'status' => $statusPendaftaran,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			));
			$this->Pendaftaran_gelombang_model->update((int) $pg->id, array(
				'status' => 'diterima_ke_kelas',
				'peserta_id' => $pesertaId,
				'kelas_id' => $kelasId,
				'catatan_admin' => $catatan !== '' ? $catatan : NULL,
				'diproses_by' => (int) $this->session->userdata('user_id'),
				'diproses_at' => date('Y-m-d H:i:s'),
			));
			$this->session->set_flashdata('sukses', 'Calon berhasil diterima ke kelas. Pendaftaran '.($statusPendaftaran === 'aktif' ? 'langsung aktif' : 'menunggu verifikasi pembayaran').'.');
			redirect('admin/gelombang/calon/'.$pg->gelombang_id);
			return;
		}
		redirect('admin/gelombang/calon/'.$pg->gelombang_id);
	}

	private function validasi()
	{
		$this->form_validation->set_rules('program_id', 'Program', 'required|integer');
		$this->form_validation->set_rules('angkatan', 'Angkatan', 'required|trim|max_length[30]');
		$this->form_validation->set_rules('tgl_buka', 'Tanggal Buka', 'required');
		$this->form_validation->set_rules('tgl_tutup', 'Tanggal Tutup', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,buka,tutup]');
		$this->form_validation->set_rules('kuota', 'Kuota', 'trim|integer|greater_than_equal_to[0]');
		if ($this->form_validation->run() === FALSE) {
			return FALSE;
		}
		$buka = $this->input->post('tgl_buka');
		$tutup = $this->input->post('tgl_tutup');
		if ($buka && $tutup && $tutup < $buka) {
			$this->session->set_flashdata('gagal', 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
			return FALSE;
		}
		return TRUE;
	}

	private function dataInput()
	{
		$kuota = $this->input->post('kuota');
		$kuota = $kuota === '' || $kuota === NULL ? NULL : (int) $kuota;
		$ket = trim((string) $this->input->post('keterangan'));
		return array(
			'program_id' => (int) $this->input->post('program_id'),
			'angkatan' => trim((string) $this->input->post('angkatan')),
			'tgl_buka' => $this->input->post('tgl_buka'),
			'tgl_tutup' => $this->input->post('tgl_tutup'),
			'kuota' => $kuota,
			'keterangan' => $ket !== '' ? $ket : NULL,
			'status' => $this->input->post('status'),
		);
	}

	private function ambil($id)
	{
		$g = (int) $id > 0 ? $this->Gelombang_model->find((int) $id) : NULL;
		if (! $g) {
			show_404();
			exit;
		}
		return $g;
	}
}
