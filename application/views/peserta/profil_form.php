<?php defined('BASEPATH') OR exit('No direct script access allowed');
$val = function ($field, $default = '') use ($edit) {
	if (set_value($field) !== '') {
		return set_value($field);
	}
	return ($edit !== NULL && isset($edit->$field) && $edit->$field !== NULL) ? $edit->$field : $default;
};
$err = function ($field) {
	return form_error($field);
};
$aksi = $edit === NULL ? 'peserta/profil/simpan' : 'peserta/profil/update/'.$edit->id;
?>
<div class="mx-auto max-w-2xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Peserta' : 'Edit Data Peserta' ?></h1>
		<a href="<?= site_url('peserta/profil') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>

	<div class="card">
		<div class="card-body">
		<?= form_open($aksi) ?>
			<div class="grid gap-4 sm:grid-cols-2">
				<div class="sm:col-span-2">
					<label class="label" for="nama">Nama Peserta <span class="text-rose-600">*</span></label>
					<input type="text" id="nama" name="nama" value="<?= html_escape($val('nama')) ?>" required class="input">
					<?= $err('nama') ?>
				</div>
				<div>
					<label class="label" for="jenis_kelamin">Jenis Kelamin <span class="text-rose-600">*</span></label>
					<select id="jenis_kelamin" name="jenis_kelamin" required class="input">
						<?= pilih_opsi(array('' => '— Pilih —', 'L' => 'Laki-laki', 'P' => 'Perempuan'), $val('jenis_kelamin')) ?>
					</select>
					<?= $err('jenis_kelamin') ?>
				</div>
				<div>
					<label class="label" for="tanggal_lahir">Tanggal Lahir</label>
					<input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= html_escape($val('tanggal_lahir')) ?>" class="input">
					<?= $err('tanggal_lahir') ?>
				</div>
				<div>
					<label class="label" for="no_wa">No. WhatsApp <span class="text-rose-600">*</span></label>
					<input type="text" id="no_wa" name="no_wa" value="<?= html_escape($val('no_wa')) ?>" required
						placeholder="0812... / +62812..." class="input">
					<?= $err('no_wa') ?>
				</div>
				<div>
					<label class="label" for="kemampuan_awal">Kemampuan Awal <span class="text-rose-600">*</span></label>
					<select id="kemampuan_awal" name="kemampuan_awal" required class="input">
						<?= pilih_opsi(array('' => '— Pilih —', 'pemula' => 'Pemula', 'hafalan' => 'Sudah punya hafalan'), $val('kemampuan_awal')) ?>
					</select>
					<?= $err('kemampuan_awal') ?>
				</div>
				<div>
					<label class="label" for="nama_wali">Nama Wali <span class="font-normal text-slate-400">(opsional)</span></label>
					<input type="text" id="nama_wali" name="nama_wali" value="<?= html_escape($val('nama_wali')) ?>" class="input">
					<?= $err('nama_wali') ?>
				</div>
				<div class="sm:col-span-2">
					<label class="label" for="alamat">Alamat <span class="font-normal text-slate-400">(opsional)</span></label>
					<textarea id="alamat" name="alamat" rows="2" class="input"><?= html_escape($val('alamat')) ?></textarea>
					<?= $err('alamat') ?>
				</div>
			</div>

			<div class="mt-6 flex items-center gap-3">
				<button type="submit" class="btn-primary">
					<?= $edit === NULL ? 'Simpan' : 'Perbarui' ?>
				</button>
				<a href="<?= site_url('peserta/profil') ?>" class="btn-ghost">Batal</a>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>
