<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-2xl px-4 py-8">
	<div class="mb-5">
		<h1 class="text-2xl font-bold text-ink">Daftar Minat</h1>
		<p class="text-muted"><?= html_escape($gelombang->program_kode.' - '.$gelombang->program_nama) ?> | <?= html_escape($gelombang->angkatan) ?> (<?= tanggal_indo($gelombang->tgl_buka) ?> s/d <?= tanggal_indo($gelombang->tgl_tutup) ?>)</p>
	</div>
	<div class="card"><div class="card-body">
		<?= form_open('gelombang/daftar/'.$gelombang->id) ?>
		<div class="grid gap-4">
			<div>
				<label class="label">Nama Calon Peserta <span class="text-rose-600">*</span></label>
				<input type="text" name="nama_calon" value="<?= html_escape(set_value('nama_calon', isset($peserta->nama) ? $peserta->nama : '')) ?>" required class="input">
			</div>
			<div>
				<label class="label">No. HP <span class="text-rose-600">*</span></label>
				<input type="text" name="no_hp" value="<?= html_escape(set_value('no_hp', isset($peserta->no_hp) ? $peserta->no_hp : '')) ?>" required class="input">
			</div>
			<div>
				<label class="label">Email</label>
				<input type="email" name="email" value="<?= html_escape(set_value('email', isset($user->email) ? $user->email : '')) ?>" class="input">
			</div>
		</div>
		<div class="mt-6 flex gap-3">
			<button class="btn-primary">Kirim Pendaftaran Minat</button>
			<a href="<?= site_url('gelombang') ?>" class="btn-ghost">Batal</a>
		</div>
		<?= form_close() ?>
	</div></div>
</div>
