<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-md">
	<div class="card p-6 sm:p-7">
		<h1 class="text-xl font-bold text-ink">Login</h1>
		<p class="mb-5 mt-1 text-sm text-muted">Masuk dengan email atau username Anda.</p>

		<?= validation_errors('<div class="mb-3 rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700">', '</div>') ?>

		<?= form_open('auth/login') ?>
			<div class="mb-4">
				<label class="label" for="identifier">Email atau Username</label>
				<input type="text" id="identifier" name="identifier" value="<?= html_escape(set_value('identifier')) ?>"
					class="input" autocomplete="username" required>
			</div>
			<div class="mb-5">
				<label class="label" for="password">Password</label>
				<input type="password" id="password" name="password" class="input" autocomplete="current-password" required>
			</div>
			<button type="submit" class="btn-primary w-full">Masuk</button>
		<?= form_close() ?>

		<p class="mt-5 text-center text-sm text-muted">
			Belum punya akun?
			<a href="<?= site_url('auth/registrasi') ?>" class="font-semibold text-primary hover:underline">Registrasi</a>
		</p>
	</div>
</div>
