<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Helper format tampilan (Bahasa Indonesia, Rupiah, zona Asia/Jakarta)
|--------------------------------------------------------------------------
*/

if (! function_exists('rupiah'))
{
	/**
	 * Format integer Rupiah → "Rp 1.500.000".
	 */
	function rupiah($nominal)
	{
		return 'Rp '.number_format((int) $nominal, 0, ',', '.');
	}
}

if (! function_exists('tanggal_indo'))
{
	/**
	 * Format tanggal 'Y-m-d' atau 'Y-m-d H:i:s' → "19 Oktober 2026".
	 * Bila kosong/tidak valid → "-".
	 */
	function tanggal_indo($tanggal)
	{
		if ($tanggal === NULL || $tanggal === '') {
			return '-';
		}
		$ts = strtotime($tanggal);
		if ($ts === FALSE) {
			return '-';
		}
		$bulan = array(
			1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
		);
		return (int) date('j', $ts).' '.$bulan[(int) date('n', $ts)].' '.date('Y', $ts);
	}
}

if (! function_exists('nama_bulan'))
{
	/**
	 * Periode 'YYYY-MM' → "Oktober 2026".
	 */
	function nama_bulan($periode)
	{
		$bulan = array(
			1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
		);
		if (! is_string($periode) || ! preg_match('/^(\d{4})-(\d{2})$/', $periode, $m)) {
			return (string) $periode;
		}
		return $bulan[(int) $m[2]].' '.$m[1];
	}
}

if (! function_exists('periode_ini'))
{
	/** Periode bulan berjalan, format 'YYYY-MM'. */
	function periode_ini()
	{
		return date('Y-m');
	}
}

if (! function_exists('rentang_periode'))
{
	/**
	 * Periode 'YYYY-MM' → array rentang datetime untuk query pemasukan:
	 * array('2026-10-01 00:00:00', '2026-11-01 00:00:00') — akhir bersifat exclusive.
	 * Periode tidak valid jatuh ke bulan berjalan.
	 */
	function rentang_periode($periode)
	{
		if (! is_string($periode) || ! preg_match('/^(\d{4})-(\d{2})$/', $periode)) {
			$periode = periode_ini();
		}
		$mulai = $periode.'-01 00:00:00';
		$akhir = date('Y-m-01 00:00:00', strtotime($mulai.' +1 month'));
		return array($mulai, $akhir);
	}
}

if (! function_exists('label_kelas'))
{
	/**
	 * Label kelas dari baris hasil join Kelas_model:
	 * "BIHAR · Level 1 · Condet · Online".
	 * (Pemanggil wajib html_escape() bila kolom berasal dari input admin.)
	 */
	function label_kelas($row)
	{
		if (! is_array($row)) {
			return '-';
		}
		$parts = array(
			isset($row['program_kode']) ? $row['program_kode'] : '?',
			isset($row['level_nama']) ? $row['level_nama'] : '?',
			isset($row['cabang_nama']) ? $row['cabang_nama'] : '?',
			isset($row['mode']) ? ucfirst($row['mode']) : '?',
		);
		return implode(' · ', $parts);
	}
}

if (! function_exists('pilih_opsi'))
{	/**
	 * Bantu render <option>: pilih_opsi($list, $terpilih) menghasilkan
	 * baris option dengan selected pada nilai terpilih. $list = [value => label].
	 * Dipanggil dari view: echo pilih_opsi(...).
	 */
	function pilih_opsi($list, $terpilih = NULL)
	{
		$html = '';
		foreach ((array) $list as $value => $label) {
			$sel = ((string) $value === (string) $terpilih) ? ' selected' : '';
			$html .= '<option value="'.html_escape($value).'"'.$sel.'>'.html_escape($label).'</option>';
		}
		return $html;
	}
}

if (! function_exists('program_gambar'))
{
	/**
	 * Path gambar program untuk carousel/hero.
	 * Mengembalikan NULL bila file belum tersedia → view memakai fallback
	 * gradient + inisial (halaman tetap rapi walau gambar belum diunduh).
	 *
	 * @return string|null URL gambar atau NULL
	 */
	function program_gambar($kode)
	{
		$map = array(
			'BIHAR' => 'program-bihar.jpg',
			'BADAL' => 'program-badal.jpg',
			'BILAL' => 'program-bilal.jpg',
			'PDHQ' => 'program-pdhq.jpg',
		);
		$kode = strtoupper((string) $kode);
		if (! isset($map[$kode])) {
			return NULL;
		}
		if (! is_file(FCPATH.'assets/img/'.$map[$kode])) {
			return NULL;
		}
		return base_url('assets/img/'.$map[$kode]);
	}
}

if (! function_exists('program_gambar_list'))
{
	/**
	 * Daftar gambar program (satu gambar per program) untuk slider hero beranda.
	 * Hanya mengembalikan berkas yang benar-benar ada; fallback ke hero.jpg.
	 *
	 * @return array [ ['img' => url, 'kode' => 'BIHAR', 'nama' => 'BIHAR'], ... ]
	 */
	function program_gambar_list()
	{
		$daftar = array(
			array('file' => 'program-bihar.jpg', 'kode' => 'BIHAR'),
			array('file' => 'program-badal.jpg', 'kode' => 'BADAL'),
			array('file' => 'program-bilal.jpg', 'kode' => 'BILAL'),
			array('file' => 'program-pdhq.jpg', 'kode' => 'PDHQ'),
		);

		$out = array();
		foreach ($daftar as $d) {
			if (is_file(FCPATH.'assets/img/'.$d['file'])) {
				$out[] = array(
					'img' => base_url('assets/img/'.$d['file']),
					'kode' => $d['kode'],
					'nama' => $d['kode'],
				);
			}
		}

		if (empty($out) && is_file(FCPATH.'assets/img/hero.jpg')) {
			$out[] = array(
				'img' => base_url('assets/img/hero.jpg'),
				'kode' => 'HERO',
				'nama' => 'Kelas Al-Utsmani',
			);
		}

		return $out;
	}
}

if (! function_exists('hero_gambar'))
{
	/** @return string|null URL gambar hero beranda, NULL bila belum ada. */
	function hero_gambar()
	{
		if (! is_file(FCPATH.'assets/img/hero.jpg')) {
			return NULL;
		}
		return base_url('assets/img/hero.jpg');
	}
}

if (! function_exists('logo'))
{
	/**
	 * URL logo lembaga (navbar admin/publik).
	 * Mengembalikan NULL bila berkas belum tersedia → view memakai fallback
	 * badge inisial "AU" (jangan hapus fallback ini).
	 *
	 * @return string|null URL logo atau NULL
	 */
	function logo()
	{
		if (! is_file(FCPATH.'assets/img/logo-utsmani.jpg')) {
			return NULL;
		}
		return base_url('assets/img/logo-utsmani.jpg');
	}
}

if (! function_exists('cabang_gambar'))
{
	/**
	 * Path gambar cabang berdasarkan wilayah (dipakai kartu pemilih cabang).
	 * Mengembalikan NULL bila file belum tersedia → view memakai fallback
	 * gradient + singkatan nama cabang.
	 *
	 * @return string|null URL gambar atau NULL
	 */
	function cabang_gambar($wilayah)
	{
		$map = array(
			'Jakarta Timur' => 'cabang-jakarta-timur.jpg',
			'Bekasi' => 'cabang-bekasi.jpg',
			'Cirebon' => 'cabang-cirebon.jpg',
			'Padang' => 'cabang-padang.jpg',
			'Bogor' => 'cabang-bogor.jpg',
			'Sorong' => 'cabang-sorong.jpg',
		);
		$wilayah = trim((string) $wilayah);
		if (! isset($map[$wilayah])) {
			return NULL;
		}
		if (! is_file(FCPATH.'assets/img/'.$map[$wilayah])) {
			return NULL;
		}
		return base_url('assets/img/'.$map[$wilayah]);
	}
}

if (! function_exists('singkatan'))
{
	/**
	 * Singkatan dari beberapa kata untuk fallback gambar/avatar.
	 * "Jakarta Timur" → "JT" (maks $maks huruf).
	 */
	function singkatan($teks, $maks = 3)
	{
		$teks = trim((string) $teks);
		if ($teks === '') {
			return '?';
		}
		$out = '';
		foreach (preg_split('/\s+/', $teks) as $kata) {
			$out .= strtoupper(substr($kata, 0, 1));
			if (strlen($out) >= (int) $maks) {
				break;
			}
		}
		return $out;
	}
}

if (! function_exists('baris_kelas'))
{
	/**
	 * Satu baris ringkas kelas untuk kartu program:
	 * "Level 1 · Sabtu 08.00-09.30 WIB · Condet".
	 * Zona waktu tidak ditambahkan bila sudah tercantum di teks jadwal.
	 *
	 * @param  array $k baris dari Kelas_model::rekapProgram()['kelas']
	 * @return string teks (escape lewat html_escape() oleh pemanggil)
	 */
	function baris_kelas(array $k)
	{
		$bagian = array();

		if (! empty($k['level'])) {
			$bagian[] = $k['level'];
		}

		$jadwal = isset($k['jadwal']) ? trim((string) $k['jadwal']) : '';
		$zona = isset($k['zona']) ? trim((string) $k['zona']) : '';
		if ($jadwal !== '' && $zona !== '' && stripos($jadwal, $zona) === FALSE) {
			$jadwal .= ' '.$zona;
		}
		if ($jadwal !== '') {
			$bagian[] = $jadwal;
		}

		if (! empty($k['cabang'])) {
			$bagian[] = $k['cabang'];
		}

		return implode(' · ', $bagian);
	}
}

if (! function_exists('baris_kelas_html'))
{
	/**
	 * <li> baris kelas siap tampil (badge level/jadwal/cabang + chip mode).
	 * Dipakai kartu program (pemilih & carousel) agar tampilan seragam.
	 */
	function baris_kelas_html(array $k)
	{
		$mode = isset($k['mode']) ? (string) $k['mode'] : '';
		$chip = '';
		if ($mode === 'online') {
			$chip = '<span class="shrink-0 inline-block text-[10px] font-medium rounded px-1.5 py-0.5 bg-sky-100 text-sky-800">Online</span>';
		} elseif ($mode === 'offline') {
			$chip = '<span class="shrink-0 inline-block text-[10px] font-medium rounded px-1.5 py-0.5 bg-violet-100 text-violet-800">Offline</span>';
		}

		return '<li class="flex items-start justify-between gap-2">'
			.'<span class="text-xs text-slate-600 leading-snug">'.html_escape(baris_kelas($k)).'</span>'
			.$chip.'</li>';
	}
}

if (! function_exists('badge_level'))
{
	/**
	 * Badge level/rank peserta (title profil singkat).
	 * Level kosong → "Belum ada level" (menunggu penempatan pengelola).
	 * Warna berjenjang mengikuti $urutan (Idad → Tahfidz).
	 */
	function badge_level($levelNama, $urutan = NULL)
	{
		if ($levelNama === NULL || $levelNama === '') {
			return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-slate-200 text-slate-600">Belum ada level</span>';
		}

		$warna = array(
			1 => 'bg-slate-200 text-slate-700',
			2 => 'bg-sky-100 text-sky-800',
			3 => 'bg-teal-100 text-teal-800',
			4 => 'bg-emerald-100 text-emerald-800',
			5 => 'bg-amber-100 text-amber-800',
			6 => 'bg-orange-100 text-orange-800',
			7 => 'bg-purple-100 text-purple-800',
			8 => 'bg-rose-100 text-rose-800',
		);
		$kelas = isset($warna[(int) $urutan]) ? $warna[(int) $urutan] : 'bg-emerald-100 text-emerald-800';

		return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold '.$kelas.'">'.html_escape($levelNama).'</span>';
	}
}

if (! function_exists('badge'))
{
	/**
	 * Badge status dengan warna; input dikunci lewat whitelist (aman tanpa escape).
	 * Dipanggil dari view: echo badge_pendaftaran($row->status); dsb.
	 */
	function badge($grup, $nilai)
	{
		$map = array(
			'pendaftaran' => array(
				'menunggu' => array('Menunggu Verifikasi', 'bg-amber-100 text-amber-800'),
				'aktif' => array('Aktif', 'bg-emerald-100 text-emerald-800'),
				'ditolak' => array('Ditolak', 'bg-red-100 text-red-800'),
				'batal' => array('Batal', 'bg-slate-200 text-slate-600'),
			),
			'pembayaran' => array(
				'menunggu' => array('Menunggu', 'bg-amber-100 text-amber-800'),
				'diverifikasi' => array('Terverifikasi', 'bg-emerald-100 text-emerald-800'),
				'ditolak' => array('Ditolak', 'bg-red-100 text-red-800'),
			),
			'absensi' => array(
				'hadir' => array('Hadir', 'bg-emerald-100 text-emerald-800'),
				'izin' => array('Izin', 'bg-sky-100 text-sky-800'),
				'sakit' => array('Sakit', 'bg-amber-100 text-amber-800'),
				'alpa' => array('Alpa', 'bg-red-100 text-red-800'),
			),
			'tagihan' => array(
				'belum' => array('Belum Lunas', 'bg-amber-100 text-amber-800'),
				'lunas' => array('Lunas', 'bg-emerald-100 text-emerald-800'),
			),
			'status' => array(
				'aktif' => array('Aktif', 'bg-emerald-100 text-emerald-800'),
				'nonaktif' => array('Nonaktif', 'bg-slate-200 text-slate-600'),
			),
		);

		$teks = html_escape((string) $nilai);
		$kelas = 'bg-slate-200 text-slate-600';

		if (isset($map[$grup][(string) $nilai])) {
			$teks = $map[$grup][(string) $nilai][0];
			$kelas = $map[$grup][(string) $nilai][1];
		}

		return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium '.$kelas.'">'.$teks.'</span>';
	}
}

/**
 * Gabungan tampilan hari + jadwal + zona waktu untuk kelas.
 * Mengutamakan daftar hari dari checkbox (kolom hari) bila ada.
 */
if (! function_exists('jadwal_tampil')) {
	function jadwal_tampil($kelas)
	{
		if (! $kelas) {
			return '';
		}
		$bagian = array();
		if (isset($kelas->hari) && trim((string) $kelas->hari) !== '') {
			$hari = str_replace(',', ', ', trim((string) $kelas->hari));
			$bagian[] = $hari;
		}
		if (isset($kelas->jadwal) && trim((string) $kelas->jadwal) !== '') {
			$bagian[] = trim((string) $kelas->jadwal);
		}
		$out = implode(' | ', $bagian);
		if ($out === '' && isset($kelas->zona_waktu) && trim((string) $kelas->zona_waktu) !== '') {
			return trim((string) $kelas->zona_waktu);
		}
		if ($out !== '' && isset($kelas->zona_waktu) && trim((string) $kelas->zona_waktu) !== '') {
			$low = strtolower($out);
			$zonaLow = strtolower(trim((string) $kelas->zona_waktu));
			if (strpos($low, $zonaLow) === FALSE) {
				$out .= ' ('.trim((string) $kelas->zona_waktu).')';
			}
			return $out;
		}
		if ($out !== '') {
			return $out;
		}
		if (isset($kelas->zona_waktu) && trim((string) $kelas->zona_waktu) !== '') {
			return trim((string) $kelas->zona_waktu);
		}
		return '';
	}
}