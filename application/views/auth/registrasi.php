<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-md">
	<div class="card p-6 sm:p-7">
		<h1 class="text-xl font-bold text-ink">Registrasi Akun</h1>
		<p class="mb-5 mt-1 text-sm text-muted">
			Untuk peserta maupun wali/orang tua. Satu akun bisa menaungi beberapa peserta (mis. anak-anak).
		</p>

		<?= validation_errors('<div class="mb-3 rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700">', '</div>') ?>

		<?= form_open('auth/registrasi') ?>
			<div class="mb-4">
				<label class="label" for="nama">Nama Lengkap</label>
				<input type="text" id="nama" name="nama" value="<?= html_escape(set_value('nama')) ?>" maxlength="100" class="input" required>
			</div>
			<div class="mb-4">
				<label class="label" for="username">Username</label>
				<input type="text" id="username" name="username" value="<?= html_escape(set_value('username')) ?>" maxlength="50"
					class="input" pattern="[A-Za-z0-9_.]{3,50}" title="3–50 karakter: huruf, angka, titik, atau garis bawah">
				<p class="mt-1 text-xs text-muted">Huruf, angka, titik (.) atau garis bawah (_), minimal 3 karakter.</p>
			</div>
			<div class="mb-4">
				<label class="label" for="email">Email</label>
				<input type="email" id="email" name="email" value="<?= html_escape(set_value('email')) ?>" maxlength="100" class="input" required>
			</div>
			<div class="mb-4">
				<label class="label" for="no_wa">No. WhatsApp</label>
				<input type="text" id="no_wa" name="no_wa" value="<?= html_escape(set_value('no_wa')) ?>" maxlength="20"
					class="input" placeholder="0812xxxxxxx" required>
			</div>
			<div class="mb-4">
				<label class="label" for="password">Password</label>
				<input type="password" id="password" name="password" class="input" required>
				<p class="mt-1 text-xs text-muted">Minimal 8 karakter.</p>
			</div>
			<div class="mb-5">
				<label class="label" for="konfirmasi_password">Konfirmasi Password</label>
				<input type="password" id="konfirmasi_password" name="konfirmasi_password" class="input" required>
			</div>
			<button type="submit" class="btn-primary w-full">Daftar Akun</button>
		<?= form_close() ?>

		<p class="mt-5 text-center text-sm text-muted">
			Sudah punya akun?
			<a href="<?= site_url('auth/login') ?>" class="font-semibold text-primary hover:underline">Login</a>
		</p>
	</div>
</div>
