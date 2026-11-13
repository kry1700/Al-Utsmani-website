<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Pendaftaran</p>
			<h1 class="au-home-heading au-home-heading--h1">Daftar Kelas</h1>
			<p class="au-home-body au-home-body--lg">Pilih cabang lalu program (atau sebaliknya), kemudian pilih kelas yang sesuai jadwal Anda.</p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('katalog') ?>" class="au-home-button au-home-button-secondary">
				Lihat Katalog Program
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
			</a>
		</div>
	</header>

	<div class="au-detail-grid">
		<div>
			<?php $this->load->view('daftar/_pilih', array(
				'programs' => $programs,
				'cabangs' => $cabangs,
				'rekap' => $rekap,
				'aksi' => site_url('daftar/kelas'),
			)); ?>
		</div>

		<aside class="au-detail-stack">
			<section class="au-panel">
				<div class="au-panel__head">
					<h2 class="au-panel__title">Cara mendaftar</h2>
				</div>
				<div class="au-panel__body">
					<ol class="au-home-body au-steps">
						<li>Pilih <strong>cabang</strong> dan <strong>program</strong> (boleh dari arah mana pun).</li>
						<li>Pilih kelas yang sesuai jadwal Anda.</li>
						<li>Isi data registrasi (akun + data peserta) dan unggah bukti bila ada biaya.</li>
						<li>Tunggu verifikasi admin — pendaftaran aktif setelah disetujui.</li>
						<li>Level peserta ditetapkan pengelola lewat proses <strong>penempatan</strong>.</li>
					</ol>
				</div>
			</section>

			<section class="au-panel au-panel--cream">
				<div class="au-panel__body">
					<h2 class="au-panel__title mb-1">Sudah punya akun?</h2>
					<p class="au-home-body mb-4">Login dulu untuk mendaftarkan anak/Anda sendiri tanpa mengisi data akun lagi.</p>
					<a href="<?= site_url('auth/login') ?>" class="au-home-button au-home-button-primary au-home-button--sm">Login</a>
				</div>
			</section>
		</aside>
	</div>
</div>
