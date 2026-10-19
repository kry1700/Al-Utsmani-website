<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Absensi & progres per pertemuan (diisi guru pengampu).
 * Satu form berisi seluruh peserta aktif kelas: status kehadiran +
 * halaman mulai/selesai + catatan guru.
 */
class Absensi extends Guru_Controller {

	/** Tampilkan form isian untuk sebuah pertemuan (dengan prefill). */
	public function isi($pertemuanId = 0)
	{
		$pertemuan = $this->pertemuanMilik($pertemuanId);
		$peserta = $this->Pendaftaran_model->pesertaByKelas((int) $pertemuan->kelas_id, 'aktif');

		// Prefill: peta isian yang sudah tersimpan (satu query).
		$isian = array();
		foreach ($this->Pertemuan_model->isian((int) $pertemuan->id) as $row) {
			$isian[(int) $row->peserta_id] = $row;
		}

		$this->render('guru/absensi_form', array(
			'title' => 'Isi Absensi & Progres',
			'pertemuan' => $pertemuan,
			'peserta' => $peserta,
			'isian' => $isian,
		));
	}

	/** Simpan seluruh isian form absensi satu pertemuan (POST). */
	public function simpan($pertemuanId = 0)
	{
		$pertemuan = $this->pertemuanMilik($pertemuanId);

		if ($this->input->method() !== 'post') {
			redirect('guru/absensi/isi/'.$pertemuan->id);
			return;
		}

		$daftarPeserta = $this->Pendaftaran_model->pesertaByKelas((int) $pertemuan->kelas_id, 'aktif');
		$rows = array();
		$gagal = '';

		foreach ($daftarPeserta as $peserta) {
			$pid = (int) $peserta->id;
			$status = (string) $this->input->post('status_'.$pid);
			if (! in_array($status, array('hadir', 'izin', 'sakit', 'alpa'), TRUE)) {
				$gagal = 'Status kehadiran '.$peserta->nama.' belum dipilih.';
				break;
			}

			$halMulai = $this->angkaKosong($this->input->post('hal_mulai_'.$pid));
			$halSelesai = $this->angkaKosong($this->input->post('hal_selesai_'.$pid));

			// Halaman selesai tidak boleh lebih kecil dari halaman mulai.
			if ($halMulai !== NULL && $halSelesai !== NULL && $halSelesai < $halMulai) {
				$gagal = 'Halaman selesai '.$peserta->nama.' lebih kecil dari halaman mulai.';
				break;
			}

			$rows[] = array(
				'peserta_id' => $pid,
				'status' => $status,
				'hal_mulai' => $halMulai,
				'hal_selesai' => $halSelesai,
				'catatan_guru' => trim((string) $this->input->post('catatan_'.$pid)),
			);
		}

		if ($gagal !== '' || ! $rows) {
			$this->session->set_flashdata('gagal', $gagal !== '' ? $gagal : 'Tidak ada peserta aktif untuk diabsen.');
			redirect('guru/absensi/isi/'.$pertemuan->id);
			return;
		}

		if (! $this->Absensi_model->simpanBulk((int) $pertemuan->id, $rows)) {
			$this->session->set_flashdata('gagal', 'Absensi gagal disimpan. Coba lagi.');
			redirect('guru/absensi/isi/'.$pertemuan->id);
			return;
		}

		$this->session->set_flashdata('sukses', 'Absensi & progres pertemuan '.$pertemuan->nomor.' berhasil disimpan.');
		redirect('guru/kelas/detail/'.$pertemuan->kelas_id);
	}

	/** Riwayat absensi seluruh kelas guru untuk satu pertemuan (opsional). */
	public function riwayat($pertemuanId = 0)
	{
		$pertemuan = $this->pertemuanMilik($pertemuanId);
		$this->render('guru/absensi_riwayat', array(
			'title' => 'Riwayat Absensi Pertemuan',
			'pertemuan' => $pertemuan,
			'isian' => $this->Pertemuan_model->isian((int) $pertemuan->id),
		));
	}

	/**
	 * Ambil pertemuan + pastikan kelasnya diampu guru login; selain itu 403.
	 *
	 * @return object
	 */
	private function pertemuanMilik($pertemuanId)
	{
		$pertemuan = (int) $pertemuanId > 0 ? $this->Pertemuan_model->find((int) $pertemuanId) : NULL;
		if (! $pertemuan || (int) $pertemuan->guru_id !== (int) $this->guru->id) {
			show_error('Pertemuan tidak ditemukan atau bukan pada kelas yang Anda ampu.', 403, 'Akses Ditolak');
			exit;
		}
		return $pertemuan;
	}

	/** Input halaman: kosong → NULL, selain itu int >= 0 (NULL bila tidak valid). */
	private function angkaKosong($nilai)
	{
		$nilai = trim((string) $nilai);
		if ($nilai === '' || ! ctype_digit($nilai)) {
			return NULL;
		}
		return (int) $nilai;
	}
}
