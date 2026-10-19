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
$aksi = $edit === NULL ? 'admin/kelas/simpan' : 'admin/kelas/update/'.$edit->id;
?>
<div class="mx-auto max-w-3xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title"><?= $edit === NULL ? 'Tambah Kelas' : 'Edit Kelas' ?></h1>
		<a href="<?= site_url('admin/kelas') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>

	<div class="card">
		<div class="card-body">
		<?= form_open($aksi) ?>
			<div class="grid gap-4 sm:grid-cols-2">
				<div>
					<label class="label" for="program_id">Program <span class="text-rose-600">*</span></label>
					<select id="program_id" name="program_id" required class="input">
						<option value="">— Pilih Program —</option>
						<?php foreach ($programList as $p): ?>
							<option value="<?= (int) $p->id ?>" data-kode="<?= html_escape($p->kode) ?>" <?= (string) $val('program_id') === (string) $p->id ? 'selected' : '' ?>>
								<?= html_escape($p->kode.' — '.$p->nama) ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?= form_error('program_id') ?>
				</div>
				<div>
					<label class="label" for="level_id">Level <span class="text-rose-600">*</span></label>
					<select id="level_id" name="level_id" required class="input">
						<option value="">— Pilih Level —</option>
						<?php foreach ($levelList as $l): ?>
							<option value="<?= (int) $l->id ?>" <?= (string) $val('level_id') === (string) $l->id ? 'selected' : '' ?>><?= html_escape($l->nama) ?></option>
						<?php endforeach; ?>
					</select>
					<?= form_error('level_id') ?>
				</div>

				<div>
					<label class="label" for="cabang_id">Cabang <span class="text-rose-600">*</span></label>
					<select id="cabang_id" name="cabang_id" required class="input">
						<option value="">— Pilih Cabang —</option>
						<?php foreach ($cabangList as $c): ?>
							<option value="<?= (int) $c->id ?>" data-wilayah="<?= html_escape($c->wilayah) ?>" <?= (string) $val('cabang_id') === (string) $c->id ? 'selected' : '' ?>>
								<?= html_escape($c->nama.' ('.$c->wilayah.')') ?><?= $c->status !== 'aktif' ? ' — nonaktif' : '' ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p id="catatan-pdhq" class="mt-1 hidden rounded-xl bg-amber-50 px-3 py-2 text-xs text-amber-700">Program PDHQ hanya boleh cabang wilayah Bogor dan mode offline.</p>
					<?= form_error('cabang_id') ?>
				</div>
				<div>
					<label class="label" for="guru_id">Guru Pengampu <span class="text-rose-600">*</span></label>
					<select id="guru_id" name="guru_id" required class="input">
						<option value="">— Pilih Guru —</option>
						<?= pilih_opsi($guruOptions, $val('guru_id')) ?>
					</select>
					<?= form_error('guru_id') ?>
				</div>

				<div>
					<label class="label" for="mode">Mode <span class="text-rose-600">*</span></label>
					<select id="mode" name="mode" required class="input">
						<?= pilih_opsi(array('online' => 'Online', 'offline' => 'Offline'), $val('mode', 'offline')) ?>
					</select>
					<?= form_error('mode') ?>
				</div>
				<div>
					<label class="label" for="zona_waktu">Zona Waktu <span class="text-rose-600">*</span></label>
					<select id="zona_waktu" name="zona_waktu" required class="input">
						<?= pilih_opsi(array('WIB' => 'WIB', 'WITA' => 'WITA', 'WIT' => 'WIT'), $val('zona_waktu', 'WIB')) ?>
					</select>
					<?= form_error('zona_waktu') ?>
				</div>

				<div class="sm:col-span-2">
					<label class="label" for="jadwal">Jadwal <span class="text-rose-600">*</span></label>
					<input type="text" id="jadwal" name="jadwal" value="<?= html_escape($val('jadwal')) ?>" required
						placeholder="mis. Sabtu 08.00-09.30" class="input">
					<p class="mt-1 text-xs text-muted">Tulis jadwal dengan zona waktu di kolom Zona Waktu.</p>
					<?= form_error('jadwal') ?>
				</div>

				<div id="blok-link" class="sm:col-span-2">
					<label class="label" for="link_meeting">Link Meeting</label>
					<input type="text" id="link_meeting" name="link_meeting" value="<?= html_escape($val('link_meeting')) ?>"
						placeholder="https://meet.google.com/..." class="input">
					<p class="mt-1 text-xs text-muted">Wajib untuk kelas online — hanya ditampilkan kepada peserta terdaftar dan guru pengampu.</p>
					<?= form_error('link_meeting') ?>
				</div>

				<div>
					<label class="label" for="biaya_pendaftaran">Biaya Pendaftaran (Rp) <span class="text-rose-600">*</span></label>
					<input type="number" min="0" step="1000" id="biaya_pendaftaran" name="biaya_pendaftaran" value="<?= html_escape($val('biaya_pendaftaran', '0')) ?>" required class="input">
					<p class="mt-1 text-xs text-muted">Isi 0 bila gratis (pendaftaran langsung aktif).</p>
					<?= form_error('biaya_pendaftaran') ?>
				</div>
				<div>
					<label class="label" for="biaya_spp">Biaya SPP / Bulan (Rp) <span class="text-rose-600">*</span></label>
					<input type="number" min="0" step="1000" id="biaya_spp" name="biaya_spp" value="<?= html_escape($val('biaya_spp', '0')) ?>" required class="input">
					<?= form_error('biaya_spp') ?>
				</div>

				<div id="blok-bilal" class="hidden sm:col-span-2">
					<div class="grid gap-4 rounded-xl border border-line bg-canvas p-4 sm:grid-cols-2">
						<div>
							<label class="label" for="tipe_peserta">Tipe Peserta (khusus BILAL) <span class="text-rose-600">*</span></label>
							<select id="tipe_peserta" name="tipe_peserta" class="input">
								<?= pilih_opsi(array('' => '— Pilih —', 'private' => 'Private', 'kelompok' => 'Kelompok', 'perusahaan' => 'Perusahaan', 'instansi' => 'Instansi'), $val('tipe_peserta')) ?>
							</select>
							<?= form_error('tipe_peserta') ?>
						</div>
						<div>
							<label class="label" for="nama_instansi">Nama Instansi (opsional)</label>
							<input type="text" id="nama_instansi" name="nama_instansi" value="<?= html_escape($val('nama_instansi')) ?>" class="input">
							<?= form_error('nama_instansi') ?>
						</div>
					</div>
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
				<a href="<?= site_url('admin/kelas') ?>" class="btn-ghost">Batal</a>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>

<script>
(function () {
	var program = document.getElementById('program_id');
	var mode = document.getElementById('mode');
	var blokBilal = document.getElementById('blok-bilal');
	var blokLink = document.getElementById('blok-link');
	var catatanPdhq = document.getElementById('catatan-pdhq');

	function kodeTerpilih() {
		var opt = program.options[program.selectedIndex];
		return opt ? (opt.getAttribute('data-kode') || '') : '';
	}

	function sync() {
		var kode = kodeTerpilih();
		blokBilal.classList.toggle('hidden', kode !== 'BILAL');

		var isPdhq = (kode === 'PDHQ');
		catatanPdhq.classList.toggle('hidden', !isPdhq);
		if (isPdhq) {
			mode.value = 'offline';
		}

		blokLink.classList.toggle('hidden', mode.value !== 'online');
	}

	program.addEventListener('change', sync);
	mode.addEventListener('change', sync);
	sync();
})();
</script>
