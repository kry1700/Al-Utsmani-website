<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Helper autentikasi & rate limiting
|--------------------------------------------------------------------------
*/

if (! function_exists('rate_limit'))
{
	/**
	 * Rate limiting sederhana berbasis CI cache (file driver).
	 *
	 * @param string $key     Kunci unik, mis. 'login_127.0.0.1'
	 * @param int    $max     Maksimal percobaan dalam jendela waktu
	 * @param int    $menit   Panjang jendela (menit)
	 * @return bool  TRUE bila masih diizinkan, FALSE bila sudah mencapai batas
	 */
	function rate_limit($key, $max, $menit)
	{
		$CI =& get_instance();

		// Cache CI3 adalah driver library: harus dimuat lewat load->driver(),
		// bukan load->library() (kalau tidak, kelas CI_Driver_Library tak ada).
		if (! isset($CI->cache)) {
			$CI->load->driver('cache', array('adapter' => 'file'));
		}
		if (! isset($CI->cache)) {
			// Cache tak tersedia → rate limiting dilewati daripada memblokir user.
			return TRUE;
		}

		$cacheKey = 'rl_'.md5($key);
		$now = time();
		$data = $CI->cache->get($cacheKey);

		// Jendela waktu sederhana: reset bila sudah lewat.
		if (! is_array($data) || ! isset($data['reset']) || $now > (int) $data['reset']) {
			$data = array('reset' => $now + ($menit * 60), 'count' => 0);
		}

		if ((int) $data['count'] >= (int) $max) {
			return FALSE; // masih dalam jendela pembatasan
		}

		$data['count']++;
		$CI->cache->save($cacheKey, $data, $menit * 60);
		return TRUE;
	}
}

if (! function_exists('validasi_bukti'))
{
	/**
	 * Validasi tambahan berkas bukti pembayaran SEBELUM CI Upload library:
	 * keberadaan file, kode error PHP, ukuran maksimal 2 MB, dan ekstensi.
	 * (CI Upload tetap dijalankan untuk validasi mime server-side.)
	 *
	 * @param array $files Biasanya $_FILES
	 * @return string Pesan error Bahasa Indonesia, "" bila lolos
	 */
	function validasi_bukti(array $files)
	{
		if (! isset($files['bukti']) || ! is_array($files['bukti'])) {
			return 'File bukti belum dipilih.';
		}
		$file = $files['bukti'];
		if (! isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
			return 'File bukti belum dipilih.';
		}
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return 'Unggahan gagal (kode error '.$file['error'].'). Periksa koneksi Anda.';
		}
		if (! isset($file['size']) || (int) $file['size'] > 2 * 1024 * 1024) {
			return 'Ukuran file melebihi batas maksimal 2 MB.';
		}
		$ext = strtolower(pathinfo(isset($file['name']) ? $file['name'] : '', PATHINFO_EXTENSION));
		if (! in_array($ext, array('jpg', 'jpeg', 'png', 'pdf'), TRUE)) {
			return 'Format file harus JPG, PNG, atau PDF.';
		}
		return '';
	}
}
