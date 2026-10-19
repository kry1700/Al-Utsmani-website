<?php defined('BASEPATH') OR exit('No direct script access allowed');
$val = function ($field, $default = '') use ($edit) {
	if (set_value($field) !== '') {
		return set_value($field);
	}
	if ($edit !== NULL && isset($edit->$field) && $edit->$field !== NULL) {
		return $edit->$field;
	}
	return $default;
};
$aksi = $edit === NULL ? 'admin/cabang/simpan' : 'admin/cabang/update/'.$edit->id;
?>
<div class="mx-auto max-w-xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Cabang' : 'Edit Cabang' ?></h1>
		<a href="<?= site_url('admin/cabang') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>

	<div class="card">
		<div class="card-body">
		<?= form_open($aksi) ?>
			<div class="space-y-4">
				<div>
					<label class="label" for="wilayah">Wilayah <span class="text-rose-600">*</span></label>
					<input type="text" id="wilayah" name="wilayah" list="daftar-wilayah" value="<?= html_escape($val('wilayah')) ?>" required class="input">
					<datalist id="daftar-wilayah">
						<?php foreach ($wilayah as $w): ?>
							<option value="<?= html_escape($w) ?>"></option>
						<?php endforeach; ?>
					</datalist>
					<p class="mt-1 text-xs text-muted">Contoh: Jakarta Timur, Bekasi, Cirebon, Padang, Bogor, Sorong.</p>
					<?= form_error('wilayah') ?>
				</div>
				<div>
					<label class="label" for="nama">Nama Cabang <span class="text-rose-600">*</span></label>
					<input type="text" id="nama" name="nama" value="<?= html_escape($val('nama')) ?>" required class="input">
					<?= form_error('nama') ?>
				</div>
				<div>
					<label class="label" for="status">Status</label>
					<select id="status" name="status" class="input">
						<?= pilih_opsi(array('aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'), $val('status', 'aktif')) ?>
					</select>
					<?= form_error('status') ?>
				</div>
			</div>

			<div class="mt-6 flex items-center gap-3">
				<button type="submit" class="btn-primary">Simpan</button>
				<a href="<?= site_url('admin/cabang') ?>" class="btn-ghost">Batal</a>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>
