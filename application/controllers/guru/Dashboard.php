<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard guru: ringkasan kelas yang diampu + jumlah peserta & pertemuan.
 */
class Dashboard extends Guru_Controller {

	public function index()
	{
		$kelas = $this->Kelas_model->byGuruDenganPeserta((int) $this->guru->id);

		$jumlahPeserta = 0;
		foreach ($kelas as $k) {
			$jumlahPeserta += (int) $k->jumlah_peserta;
		}

		$data = array(
			'title' => 'Dashboard Guru',
			'kelas' => $kelas,
			'jumlahKelas' => count($kelas),
			'jumlahPeserta' => $jumlahPeserta,
			'jumlahPertemuan' => $this->Pertemuan_model->jumlahByGuru((int) $this->guru->id),
		);
		$this->render('guru/dashboard', $data);
	}
}
