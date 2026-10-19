<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard peserta/wali: ringkasan kelas diikuti, tagihan, kehadiran.
 */
class Dashboard extends Peserta_Controller {

	public function index()
	{
		$profilIds = array();
		foreach ($this->profilList as $profil) {
			$profilIds[] = (int) $profil->id;
		}

		// Satu query untuk seluruh pendaftaran akun (lintas profil anak).
		$pendaftaran = $this->Pendaftaran_model->byUser($this->user['id']);

		$aktif = 0;
		foreach ($pendaftaran as $pd) {
			if ($pd->status === 'aktif') {
				$aktif++;
			}
		}

		$tagihan = $this->Tagihan_model->belumLunasUntuk($profilIds);
		$kehadiran = $this->Absensi_model->statistikUntuk($profilIds);

		// Ringkasan per profil untuk kartu "profil singkat" (title level + program/cabang utama).
		$ringkas = array();
		foreach ($this->profilList as $profil) {
			$ringkas[(int) $profil->id] = array('jumlah' => 0, 'program_kode' => NULL, 'cabang_nama' => NULL);
		}
		foreach ($pendaftaran as $pd) {
			$pid = (int) $pd->peserta_id;
			if (! isset($ringkas[$pid]) || $pd->status !== 'aktif') {
				continue;
			}
			$ringkas[$pid]['jumlah']++;
			if ($ringkas[$pid]['program_kode'] === NULL) {
				$ringkas[$pid]['program_kode'] = $pd->program_kode;
				$ringkas[$pid]['cabang_nama'] = $pd->cabang_nama;
			}
		}

		$data = array(
			'title' => 'Dashboard',
			'profilList' => $this->profilList,
			'pendaftaran' => $pendaftaran,
			'jumlahAktif' => $aktif,
			'tagihan' => $tagihan,
			'kehadiran' => $kehadiran,
			'ringkas' => $ringkas,
			// Tagihan belum lunas terbaru untuk tabel ringkas di dashboard.
			'tagihanTerbaru' => $this->filterBelumLunas($this->Tagihan_model->byUser($this->user['id'])),
		);
		$this->render('peserta/dashboard', $data);
	}

	/** Ambil maksimal 5 tagihan belum lunas terbaru. */
	private function filterBelumLunas(array $rows)
	{
		$out = array();
		foreach ($rows as $row) {
			if ($row->status === 'belum') {
				$out[] = $row;
				if (count($out) >= 5) {
					break;
				}
			}
		}
		return $out;
	}
}
