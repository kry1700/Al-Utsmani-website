<?php defined('BASEPATH') OR exit('No direct script access allowed');
$val = function ($field, $default = '') use ($edit, $akun) {
	if (set_value($field) !== '') {
		return set_value($field);
	}
	if ($edit !== NULL && isset($edit->$field) && $edit->$field !== NULL) {
		return $edit->$field;
	}
	if ($akun !== NULL && isset($akun->$field) && $akun->$field !== NULL) {
		return $akun->$field;
	}
	return $default;
};
$aksi = $edit === NULL ? 'admin/guru/simpan' : 'admin/guru/update/'.$edit->id;
?>
<div class="mx-auto max-w-2xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Guru' : 'Edit Guru' ?></h1>
		<a href="<?= site_url('admin/guru') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>

	<div class="card">
		<div class="card-body">
		<?= form_open($aksi) ?>
			<div class="grid gap-4 sm:grid-cols-2">
				<div class="sm:col-span-2">
					<label class="label" for="nama">Nama Guru <span class="text-rose-600">*</span></label>
					<input type="text" id="nama" name="nama" value="<?= html_escape($val('nama')) ?>" required class="input">
					<?= form_error('nama') ?>
				</div>
				<div>
					<label class="label" for="username">Username <span class="text-rose-600">*</span></label>
					<input type="text" id="username" name="username" value="<?= html_escape($val('username')) ?>" required class="input">
					<?= form_error('username') ?>
				</div>
				<div>
					<label class="label" for="email">Email <span class="text-rose-600">*</span></label>
					<input type="email" id="email" name="email" value="<?= html_escape($val('email')) ?>" required class="input">
					<?= form_error('email') ?>
				</div>
				<div>
					<label class="label" for="no_wa">No. WhatsApp</label>
					<input type="text" id="no_wa" name="no_wa" value="<?= html_escape($val('no_wa')) ?>" placeholder="0812..." class="input">
					<?= form_error('no_wa') ?>
				</div>
				<div>
					<label class="label" for="password">
						Password <?= $edit === NULL ? '<span class="text-rose-600">*</span>' : '<span class="font-normal text-slate-400">(kosongkan bila tidak diubah)</span>' ?>
					</label>
					<input type="password" id="password" name="password" class="input" autocomplete="new-password">
					<?= form_error('password') ?>
				</div>
			</div>

			<div class="mt-6 flex items-center gap-3">
				<button type="submit" class="btn-primary">Simpan</button>
				<a href="<?= site_url('admin/guru') ?>" class="btn-ghost">Batal</a>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>
