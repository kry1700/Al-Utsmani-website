<?php defined('BASEPATH') OR exit('No direct script access allowed');
$logoAuth = logo_home();
?>
<div class="au-home-container">
	<div class="au-auth">
		<aside class="au-auth__aside">
			<a class="au-auth__logo" href="<?= site_url('') ?>" aria-label="LBQ Al-Utsmani — Beranda">
				<?php if ($logoAuth): ?>
					<img src="<?= html_escape($logoAuth) ?>" alt="LBQ Al-Utsmani" width="2025" height="777" decoding="async">
				<?php else: ?>
					<span class="au-home-logo__fallback">LBQ <strong>Al-Utsmani</strong></span>
				<?php endif; ?>
			</a>

			<div>
				<p class="au-home-eyebrow au-home-eyebrow--light">Panel Peserta</p>
				<h2 class="au-home-heading au-home-heading--h2 mt-3">Selamat datang kembali</h2>
				<p class="au-home-body mt-3">Masuk untuk melihat jadwal kelas, absensi, progres hafalan, dan tagihan SPP peserta Anda.</p>
			</div>

			<ul class="au-auth__list">
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					<span>Satu akun bisa menaungi beberapa peserta — cocok untuk orang tua dengan beberapa anak.</span>
				</li>
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
					<span>Jadwal kelas, riwayat absensi, dan progres belajar tersaji rapi di dashboard.</span>
				</li>
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
					<span>Link kelas online hanya tampil untuk peserta terdaftar dan guru pengampu.</span>
				</li>
			</ul>
		</aside>

		<div class="au-auth__panel">
			<section class="au-panel">
				<div class="au-panel__body">
					<header class="au-auth__head">
						<p class="au-home-eyebrow">Akun</p>
						<h1 class="au-home-heading au-home-heading--h2 mt-2">Login</h1>
						<p class="au-home-body mt-2">Masuk dengan email atau username Anda.</p>
					</header>

					<?php if (validation_errors()): ?>
						<div class="au-form-errors"><?= validation_errors('<p class="au-error">', '</p>') ?></div>
					<?php endif; ?>

					<?= form_open('auth/login', array('class' => 'au-form-stack')) ?>
						<div class="au-field">
							<label class="au-label" for="identifier">Email atau Username</label>
							<input type="text" id="identifier" name="identifier" value="<?= html_escape(set_value('identifier')) ?>"
								class="au-input" autocomplete="username" required>
						</div>
						<div class="au-field">
							<label class="au-label" for="password">Password</label>
							<input type="password" id="password" name="password" class="au-input" autocomplete="current-password" required>
						</div>
						<button type="submit" class="au-home-button au-home-button-primary au-home-button--block">Masuk</button>
					<?= form_close() ?>

					<p class="au-auth__switch">
						Belum punya akun? <a href="<?= site_url('auth/registrasi') ?>">Registrasi</a>
					</p>
				</div>
			</section>

			<p class="au-auth__foot">
				<a href="<?= site_url('katalog') ?>">Lihat katalog kelas &rarr;</a>
			</p>
		</div>
	</div>
</div>
