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
				<p class="au-home-eyebrow au-home-eyebrow--light">Akun Baru</p>
				<h2 class="au-home-heading au-home-heading--h2 mt-3">Belajar Al-Qur'an lebih terarah</h2>
				<p class="au-home-body mt-3">Registrasi gratis untuk peserta maupun wali. Setelah akun aktif, Anda bisa mendaftarkan kelas dan memantau perkembangan.</p>
			</div>

			<ul class="au-auth__list">
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					<span>Akun langsung aktif — tidak perlu menunggu verifikasi email.</span>
				</li>
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
					<span>Satu akun untuk beberapa anak/wali — pilih profil saat mendaftar kelas.</span>
				</li>
				<li>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
					<span>Level dan program ditetapkan pengelola lewat penempatan, bukan dipilih sendiri.</span>
				</li>
			</ul>
		</aside>

		<div class="au-auth__panel">
			<section class="au-panel">
				<div class="au-panel__body">
					<header class="au-auth__head">
						<p class="au-home-eyebrow">Akun Baru</p>
						<h1 class="au-home-heading au-home-heading--h2 mt-2">Registrasi Akun</h1>
						<p class="au-home-body mt-2">Untuk peserta maupun wali/orang tua. Satu akun bisa menaungi beberapa peserta (mis. anak-anak).</p>
					</header>

					<?php if (validation_errors()): ?>
						<div class="au-form-errors"><?= validation_errors('<p class="au-error">', '</p>') ?></div>
					<?php endif; ?>

					<?= form_open('auth/registrasi', array('class' => 'au-form-stack')) ?>
						<div class="au-form-grid">
							<div class="au-field">
								<label class="au-label" for="nama">Nama Lengkap</label>
								<input type="text" id="nama" name="nama" value="<?= html_escape(set_value('nama')) ?>" maxlength="100" class="au-input" required>
							</div>
							<div class="au-field">
								<label class="au-label" for="no_wa">No. WhatsApp</label>
								<input type="text" id="no_wa" name="no_wa" value="<?= html_escape(set_value('no_wa')) ?>" maxlength="20"
									class="au-input" placeholder="0812xxxxxxx" required>
							</div>
							<div class="au-field">
								<label class="au-label" for="username">Username</label>
								<input type="text" id="username" name="username" value="<?= html_escape(set_value('username')) ?>" maxlength="50"
									class="au-input" pattern="[A-Za-z0-9_.]{3,50}" title="3–50 karakter: huruf, angka, titik, atau garis bawah">
								<p class="au-hint">Huruf, angka, titik (.) atau garis bawah (_), minimal 3 karakter.</p>
							</div>
							<div class="au-field">
								<label class="au-label" for="email">Email</label>
								<input type="email" id="email" name="email" value="<?= html_escape(set_value('email')) ?>" maxlength="100" class="au-input" required>
							</div>
							<div class="au-field">
								<label class="au-label" for="password">Password</label>
								<input type="password" id="password" name="password" class="au-input" required>
								<p class="au-hint">Minimal 8 karakter.</p>
							</div>
							<div class="au-field">
								<label class="au-label" for="konfirmasi_password">Konfirmasi Password</label>
								<input type="password" id="konfirmasi_password" name="konfirmasi_password" class="au-input" required>
							</div>
						</div>

						<button type="submit" class="au-home-button au-home-button-primary au-home-button--block mt-5">Daftar Akun</button>
					<?= form_close() ?>

					<p class="au-auth__switch">
						Sudah punya akun? <a href="<?= site_url('auth/login') ?>">Login</a>
					</p>
				</div>
			</section>

			<p class="au-auth__foot">
				<a href="<?= site_url('katalog') ?>">Lihat katalog kelas &rarr;</a>
			</p>
		</div>
	</div>
</div>
