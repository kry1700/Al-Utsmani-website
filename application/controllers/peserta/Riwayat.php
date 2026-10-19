<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Riwayat absensi & progres belajar peserta (per pilihan profil anak).
 */
class Riwayat extends Peserta_Controller {

	public function absensi()
	{
		$profil = $this->profilTerpilih();
		if (! $profil) {
			return;
		}

		$data = array(
			'title' => 'Riwayat Absensi',
			'profil' => $profil,
			'profilList' => $this->profilList,
			'riwayat' => $this->Absensi_model->riwayatByPeserta((int) $profil->id),
		);
		$this->render('peserta/riwayat_absensi', $data);
	}

	public function progres()
	{
		$profil = $this->profilTerpilih();
		if (! $profil) {
			return;
		}

		$data = array(
			'title' => 'Riwayat Progres Belajar',
			'profil' => $profil,
			'profilList' => $this->profilList,
			'riwayat' => $this->Absensi_model->riwayatByPeserta((int) $profil->id),
		);
		$this->render('peserta/riwayat_progres', $data);
	}

	/**
	 * Profil dari query string ?peserta=<id> (default: profil pertama).
	 * Wajib milik akun ini — selain itu 403.
	 *
	 * @return object|NULL NULL bila akun belum punya profil peserta
	 */
	private function profilTerpilih()
	{
		if (! $this->profilList) {
			$this->session->set_flashdata('gagal', 'Akun ini belum memiliki data peserta. Lengkapi dulu di halaman Profil.');
			redirect('peserta/profil');
			return NULL;
		}

		$id = (int) $this->input->get('peserta');
		if ($id > 0) {
			return $this->profilMilik($id);
		}
		return $this->profilList[0];
	}
}
