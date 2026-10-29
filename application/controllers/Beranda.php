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
		$rekap = $this->Kelas_model->rekapProgram();
		$gelombang = $this->Gelombang_model->aktifBisaDaftar();

		// Slider hero: 1 gambar per program.
		//  1) program yang punya kelas aktif  -> status 'berjalan'
		//  2) program yang sedang membuka gelombang (coming soon) -> 'akan_datang'
		// Program yang masuk dua-duanya diprioritaskan 'berjalan'.
		$slider = array();
		$dipakai = array();

		foreach ($programs as $p) {
			$pid = (int) $p->id;
			if (empty($rekap[$pid]['kelas'])) {
				continue;
			}
			$img = program_gambar($p->kode);
			if (! $img) {
				continue;
			}
			$slider[] = array(
				'img' => $img,
				'kode' => $p->kode,
				'nama' => $p->nama,
				'status' => 'berjalan',
				'angkatan' => '',
				'periode' => '',
			);
			$dipakai[] = strtoupper($p->kode);
		}

		foreach ($gelombang as $g) {
			$kode = strtoupper((string) $g->program_kode);
			if (in_array($kode, $dipakai, TRUE)) {
				continue;
			}
			$img = program_gambar($kode);
			if (! $img) {
				continue;
			}
			$slider[] = array(
				'img' => $img,
				'kode' => $g->program_kode,
				'nama' => $g->program_nama,
				'status' => 'akan_datang',
				'angkatan' => $g->angkatan,
				'periode' => tanggal_indo($g->tgl_buka).' s/d '.tanggal_indo($g->tgl_tutup),
			);
			$dipakai[] = $kode;
		}

		// Lengkapi minimal 2 slide agar slider tetap bergerak.
		if (count($slider) < 2) {
			foreach (program_gambar_list() as $s) {
				if (in_array(strtoupper($s['kode']), $dipakai, TRUE)) {
					continue;
				}
				$s['status'] = 'berjalan';
				$s['angkatan'] = '';
				$s['periode'] = '';
				$slider[] = $s;
				$dipakai[] = strtoupper($s['kode']);
				if (count($slider) >= 2) {
					break;
				}
			}
		}

		$data = array(
			'title' => 'Beranda',
			// Hero beranda tampil full-bleed (menyentuh tepi layar) sehingga
			// layout melepas batas max-w-6xl; section lain membungkus dirinya sendiri.
			'main_penuh' => TRUE,
			'programs' => $programs,
			'cabangs' => $this->Cabang_model->aktif(),
			'rekap' => $rekap,
			'jumlah_kelas' => $this->Kelas_model->countAktif(),
			'jumlah_cabang' => $this->Cabang_model->countAktif(),
			'gelombang' => $gelombang,
			'gambarHeroSlider' => $slider,
		);
		$this->render('beranda', $data);
	}
}
