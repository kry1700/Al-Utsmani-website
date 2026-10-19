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
$aksi = $edit === NULL ? 'admin/peserta/simpan' : 'admin/peserta/update/'.$edit->id;
?>
<div class="mx-auto max-w-3xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Peserta' : 'Edit Peserta' ?></h1>
		<a href="<?= $edit === NULL ? site_url('admin/peserta') : site_url('admin/peserta/detail/'.$edit->id) ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>

	<div class="card">
		<div class="card-body">
		<?= form_open($aksi) ?>
			<h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-muted">Akun Login</h2>
			<div class="grid gap-4 sm:grid-cols-2">
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
				<div class="sm:col-span-2">
					<label class="label" for="password">
						Password <?= $edit === NULL ? '<span class="text-rose-600">*</span>' : '<span class="font-normal text-slate-400">(kosongkan bila tidak diubah)</span>' ?>
					</label>
					<input type="password" id="password" name="password" class="input" autocomplete="new-password">
					<?= form_error('password') ?>
				</div>
			</div>

			<h2 class="mb-4 mt-7 text-sm font-bold uppercase tracking-wide text-muted">Profil Peserta</h2>
			<div class="grid gap-4 sm:grid-cols-2">
				<div class="sm:col-span-2">
					<label class="label" for="nama">Nama Peserta <span class="text-rose-600">*</span></label>
					<input type="text" id="nama" name="nama" value="<?= html_escape($val('nama')) ?>" required class="input">
					<?= form_error('nama') ?>
				</div>
				<div>
					<label class="label" for="jenis_kelamin">Jenis Kelamin</label>
					<select id="jenis_kelamin" name="jenis_kelamin" class="input">
						<?= pilih_opsi(array('' => '— Belum diisi —', 'L' => 'Laki-laki', 'P' => 'Perempuan'), $val('jenis_kelamin')) ?>
					</select>
					<?= form_error('jenis_kelamin') ?>
				</div>
				<div>
					<label class="label" for="tanggal_lahir">Tanggal Lahir</label>
					<input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= html_escape($val('tanggal_lahir')) ?>" class="input">
					<?= form_error('tanggal_lahir') ?>
				</div>
				<div>
					<label class="label" for="no_wa">No. WhatsApp</label>
					<input type="text" id="no_wa" name="no_wa" value="<?= html_escape($val('no_wa')) ?>" placeholder="0812..." class="input">
					<?= form_error('no_wa') ?>
				</div>
				<div>
					<label class="label" for="nama_wali">Nama Wali</label>
					<input type="text" id="nama_wali" name="nama_wali" value="<?= html_escape($val('nama_wali')) ?>" class="input">
					<?= form_error('nama_wali') ?>
				</div>
				<div>
					<label class="label" for="kemampuan_awal">Kemampuan Awal <span class="text-rose-600">*</span></label>
					<select id="kemampuan_awal" name="kemampuan_awal" required class="input">
						<?= pilih_opsi(array('pemula' => 'Pemula', 'hafalan' => 'Sudah punya hafalan'), $val('kemampuan_awal', 'pemula')) ?>
					</select>
					<?= form_error('kemampuan_awal') ?>
				</div>
				<div class="sm:col-span-2">
					<label class="label" for="alamat">Alamat</label>
					<textarea id="alamat" name="alamat" rows="2" class="input"><?= html_escape($val('alamat')) ?></textarea>
					<?= form_error('alamat') ?>
				</div>
			</div>

			<div class="mt-6 flex items-center gap-3">
				<button type="submit" class="btn-primary">Simpan</button>
				<a href="<?= site_url('admin/peserta') ?>" class="btn-ghost">Batal</a>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>
