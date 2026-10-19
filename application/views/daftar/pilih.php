<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<h1 class="page-title">Daftar Kelas</h1>
	<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-primary hover:underline">Lihat katalog program &rarr;</a>
</div>

<div class="grid gap-5 lg:grid-cols-3">
	<div class="lg:col-span-2">
		<?php $this->load->view('daftar/_pilih', array(
			'programs' => $programs,
			'cabangs' => $cabangs,
			'rekap' => $rekap,
			'aksi' => site_url('daftar/kelas'),
		)); ?>
	</div>

	<aside class="space-y-5">
		<section class="card p-5 text-sm leading-relaxed text-muted">
			<h2 class="mb-2 font-semibold text-ink">Cara Mendaftar</h2>
			<ol class="ml-5 list-decimal space-y-1">
				<li>Pilih <strong class="text-ink">cabang</strong> dan <strong class="text-ink">program</strong> (boleh dari arah mana pun).</li>
				<li>Pilih kelas yang sesuai jadwal Anda.</li>
				<li>Isi data registrasi (akun + data peserta) dan unggah bukti bila ada biaya.</li>
				<li>Tunggu verifikasi admin — pendaftaran aktif setelah disetujui.</li>
				<li>Level peserta ditetapkan pengelola lewat proses <strong class="text-ink">penempatan</strong>.</li>
			</ol>
		</section>

		<section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm leading-relaxed text-emerald-900">
			<h2 class="mb-1 font-semibold">Sudah punya akun?</h2>
			<p>Login dulu untuk mendaftarkan anak/Anda sendiri tanpa mengisi data akun lagi.</p>
			<a href="<?= site_url('auth/login') ?>" class="btn-primary mt-3">Login</a>
		</section>
	</aside>
</div>
