<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Katalog kelas publik (browsing tanpa login) + detail kelas.
 */
class Katalog extends MY_Controller {

	public function index()
	{
		// Katalog program: ditampilkan sebagai carousel (level bukan pilihan peserta).
		$programs = $this->Program_model->all();

		$data = array(
			'title' => 'Katalog Program',
			'programs' => $programs,
			'rekap' => $this->Kelas_model->rekapProgram(),
			'jumlah_kelas' => $this->Kelas_model->countAktif(),
			'cabangs' => $this->Cabang_model->aktif(),
		);
		$this->render('katalog/index', $data);
	}

	/** Daftar kelas satu program — filter cabang + mode (tanpa filter level). */
	public function program($id = 0)
	{
		$program = (int) $id > 0 ? $this->Program_model->find((int) $id) : NULL;
		if (! $program) {
			show_404();
		}

		$cabangId = (int) $this->input->get('cabang');
		$mode = (string) $this->input->get('mode');

		$filter = array('status' => 'aktif', 'program_id' => (int) $program->id);
		if ($cabangId > 0) {
			$filter['cabang_id'] = $cabangId;
		}
		if (in_array($mode, array('online', 'offline'), TRUE)) {
			$filter['mode'] = $mode;
		}

		$this->render('katalog/program', array(
			'title' => 'Program '.$program->kode,
			'program' => $program,
			'kelas' => $this->Kelas_model->daftar($filter),
			'programs' => $this->Program_model->all(),
			'cabangs' => $this->Cabang_model->aktif(),
			'rekap' => $this->Kelas_model->rekapProgram(),
			'cabangId' => $cabangId,
			'mode' => in_array($mode, array('online', 'offline'), TRUE) ? $mode : '',
		));
	}

	public function detail($id = 0)
	{
		$kelas = $id > 0 ? $this->Kelas_model->find((int) $id) : NULL;
		if (! $kelas || $kelas->status !== 'aktif') {
			show_404();
		}

		$data = array(
			'title' => 'Detail Kelas',
			'kelas' => $kelas,
			// Untuk tombol daftar & pre-pilih peserta bila login sebagai peserta/wali.
			'profilList' => array(),
			'pendaftaranSaya' => NULL,
		);

		if ($this->isLoggedIn() && $this->user['role'] === 'peserta') {
			$profilList = $this->Peserta_model->byUser($this->user['id']);
			$data['profilList'] = $profilList;

			// Sudah terdaftar? (cukup satu profil yang terdaftar untuk menampilkan status)
			foreach ($profilList as $profil) {
				$pd = $this->db->where('kelas_id', (int) $kelas->id)
					->where('peserta_id', (int) $profil->id)
					->get('pendaftaran')->row();
				if ($pd) {
					$data['pendaftaranSaya'] = $pd;
					break;
				}
			}
		}

		// Link meeting: guru pengampu ATAU peserta dengan pendaftaran aktif saja.
		$data['bolehLihatLink'] = $this->bolehLihatLink($kelas);
		$this->render('katalog/detail', $data);
	}

	/** @return bool TRUE bila user saat ini boleh melihat link meeting kelas ini. */
	private function bolehLihatLink($kelas)
	{
		if (! $this->isLoggedIn()) {
			return FALSE;
		}
		if ($this->user['role'] === 'guru') {
			$guru = $this->Guru_model->byUser($this->user['id']);
			return $guru && (int) $guru->id === (int) $kelas->guru_id;
		}
		if ($this->user['role'] === 'peserta') {
			// Pendaftaran aktif milik salah satu profil di akun ini?
			foreach ($this->Peserta_model->byUser($this->user['id']) as $profil) {
				$ada = $this->db->where('kelas_id', (int) $kelas->id)
					->where('peserta_id', (int) $profil->id)
					->where('status', 'aktif')
					->count_all_results('pendaftaran') > 0;
				if ($ada) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}
