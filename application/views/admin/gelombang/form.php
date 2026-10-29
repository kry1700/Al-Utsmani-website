<?php defined('BASEPATH') OR exit('No direct script access allowed');
$val = function ($field, $default = '') use ($edit) {
	if (set_value($field) !== '') return set_value($field);
	if ($edit !== NULL && isset($edit->$field) && $edit->$field !== NULL) return $edit->$field;
	return $default;
};
$aksi = $edit === NULL ? 'admin/gelombang/simpan' : 'admin/gelombang/update/'.$edit->id;
?>
<div class="mx-auto max-w-3xl">
	<div class="mb-5 flex items-end justify-between">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Gelombang' : 'Edit Gelombang' ?></h1>
		<a href="<?= site_url('admin/gelombang') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>
	<div class="card"><div class="card-body">
	<?= form_open($aksi) ?>
		<div class="grid gap-4 sm:grid-cols-2">
			<div class="sm:col-span-2">
				<label class="label">Program <span class="text-rose-600">*</span></label>
				<select name="program_id" required class="input">
					<option value="">— Pilih Program —</option>
					<?php foreach ($programList as $p): ?>
						<option value="<?= (int) $p->id ?>" <?= (string) $val('program_id') === (string) $p->id ? 'selected' : '' ?>><?= html_escape($p->kode.' - '.$p->nama) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="sm:col-span-2">
				<label class="label">Angkatan <span class="text-rose-600">*</span></label>
				<input type="text" name="angkatan" value="<?= html_escape($val('angkatan')) ?>" required class="input" placeholder="Contoh: Angkatan 1 / Gelombang Juli 2026">
			</div>
			<div>
				<label class="label">Tanggal Buka <span class="text-rose-600">*</span></label>
				<input type="date" name="tgl_buka" value="<?= html_escape($val('tgl_buka')) ?>" required class="input">
			</div>
			<div>
				<label class="label">Tanggal Tutup <span class="text-rose-600">*</span></label>
				<input type="date" name="tgl_tutup" value="<?= html_escape($val('tgl_tutup')) ?>" required class="input">
			</div>
			<div>
				<label class="label">Kuota (opsional)</label>
				<input type="number" min="0" name="kuota" value="<?= html_escape($val('kuota')) ?>" class="input">
			</div>
			<div>
				<label class="label">Status <span class="text-rose-600">*</span></label>
				<select name="status" required class="input">
					<?= pilih_opsi(array('draft'=>'Draft','buka'=>'Buka','tutup'=>'Tutup'), $val('status','draft')) ?>
				</select>
			</div>
			<div class="sm:col-span-2">
				<label class="label">Keterangan (opsional)</label>
				<textarea name="keterangan" class="input" rows="4"><?= html_escape($val('keterangan')) ?></textarea>
			</div>
		</div>
		<div class="mt-6 flex gap-3">
			<button class="btn-primary">Simpan</button>
			<a href="<?= site_url('admin/gelombang') ?>" class="btn-ghost">Batal</a>
		</div>
	<?= form_close() ?>
	</div></div>
</div>
