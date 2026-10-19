<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Beranda publik: hero penjelasan Al-Utsmani + pemilih cabang/program
 * (langkah awal pendaftaran) + carousel katalog program.
 */
class Beranda extends MY_Controller {

	public function index()
	{
		$programs = $this->Program_model->all();

		$data = array(
			'title' => 'Beranda',
			// Hero beranda tampil full-bleed (menyentuh tepi layar) sehingga
			// layout melepas batas max-w-6xl; section lain membungkus dirinya sendiri.
			'main_penuh' => TRUE,
			'programs' => $programs,
			'cabangs' => $this->Cabang_model->aktif(),
			'rekap' => $this->Kelas_model->rekapProgram(),
			'jumlah_kelas' => $this->Kelas_model->countAktif(),
			'jumlah_cabang' => $this->Cabang_model->countAktif(),
		);
		$this->render('beranda', $data);
	}
}
