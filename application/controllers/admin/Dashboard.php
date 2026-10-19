<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard admin: ringkasan metrik utama dengan filter periode + cabang.
 * Semua angka dihitung di Dashboard_model (1 query per metrik, tanpa N+1).
 */
class Dashboard extends Admin_Controller {

	public function index()
	{
		$periode = (string) $this->input->get('periode');
		if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
			$periode = periode_ini();
		}

		$cabangId = (int) $this->input->get('cabang');
		$filterCabang = $cabangId > 0 ? $cabangId : NULL;

		$cabangList = $this->Cabang_model->all();

		// Peta [cabang_id => nama] untuk tabel pemasukan per cabang.
		$namaCabang = array();
		foreach ($cabangList as $c) {
			$namaCabang[(int) $c->id] = $c->nama.' ('.$c->wilayah.')';
		}

		$data = array(
			'title' => 'Dashboard Admin',
			'periode' => $periode,
			'cabangId' => $filterCabang,
			'cabangList' => $cabangList,
			'namaCabang' => $namaCabang,
			'peserta' => $this->Dashboard_model->pesertaBaruLama($periode, $filterCabang),
			'belumLevel' => $this->Peserta_model->countBelumLevel(),
			'guruAktif' => $this->Dashboard_model->guruAktif($filterCabang),
			'kelasAktif' => $this->Dashboard_model->kelasAktif($filterCabang),
			'cabangAktif' => $this->Dashboard_model->cabangAktif(),
			'pemasukan' => $this->Dashboard_model->pemasukan($periode, $filterCabang),
			'pemasukanPerCabang' => $this->Dashboard_model->pemasukanPerCabang($periode),
			'tunggakan' => $this->Dashboard_model->tunggakan($filterCabang),
		);
		$this->render('admin/dashboard', $data);
	}
}
