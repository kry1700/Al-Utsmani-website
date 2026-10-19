<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perkembangan per cabang (admin): rekap REALTIME per cabang
 * (peserta aktif, program aktif, kelas aktif, guru, pemasukan, tunggakan)
 * + drill-down detail peserta aktif di sebuah cabang. Tanpa grafik.
 */
class Perkembangan extends Admin_Controller {

	public function index()
	{
		$periode = periode_ini();

		$this->render('admin/perkembangan', array(
			'title' => 'Perkembangan per Cabang',
			'periode' => $periode,
			'cabangList' => $this->Cabang_model->all(),
			'pesertaPerCabang' => $this->Dashboard_model->pesertaAktifPerCabang(),
			'programPerCabang' => $this->Dashboard_model->programAktifPerCabang(),
			'kelasPerCabang' => $this->Dashboard_model->kelasAktifPerCabang(),
			'guruPerCabang' => $this->Dashboard_model->guruPerCabang(),
			'pemasukanPerCabang' => $this->Dashboard_model->pemasukanPerCabang($periode),
			'tunggakanPerCabang' => $this->Dashboard_model->tunggakanPerCabang(),
		));
	}

	/** Detail peserta aktif + statistik kehadiran & progres terakhir. */
	public function detail($cabangId = 0)
	{
		$cabang = (int) $cabangId > 0 ? $this->Cabang_model->find((int) $cabangId) : NULL;
		if (! $cabang) {
			show_404();
			return;
		}

		$this->render('admin/perkembangan_detail', array(
			'title' => 'Detail Peserta Aktif — '.$cabang->nama,
			'cabang' => $cabang,
			'detail' => $this->Dashboard_model->pesertaAktifDetail((int) $cabang->id),
			'absensi' => $this->Dashboard_model->absensiDetail((int) $cabang->id),
		));
	}
}
