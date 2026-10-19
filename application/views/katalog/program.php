<?php defined('BASEPATH') OR exit('No direct script access allowed');
$perWilayah = array();
foreach ($cabangs as $c) {
	$perWilayah[$c->wilayah][] = $c;
}
?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">
			<span class="text-primary"><?= html_escape($program->kode) ?></span> — <?= html_escape($program->nama) ?>
		</h1>
		<p class="mt-1 max-w-3xl text-sm text-muted"><?= html_escape($program->deskripsi) ?></p>
	</div>
	<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Katalog Program</a>
</div>

<form method="get" action="<?= site_url('katalog/program/'.(int) $program->id) ?>" class="card mb-6 flex flex-wrap items-end gap-3 p-5">
	<div class="min-w-48">
		<label class="label" for="cabang">Cabang</label>
		<select id="cabang" name="cabang" class="input">
			<option value="">Semua cabang</option>
			<?php foreach ($perWilayah as $wilayah => $daftar): ?>
				<optgroup label="<?= html_escape($wilayah) ?>">
					<?php foreach ($daftar as $c): ?>
						<option value="<?= (int) $c->id ?>" <?= (int) $cabangId === (int) $c->id ? 'selected' : '' ?>><?= html_escape($c->nama) ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="min-w-40">
		<label class="label" for="mode">Mode</label>
		<select id="mode" name="mode" class="input">
			<option value="">Online &amp; Offline</option>
			<option value="online" <?= $mode === 'online' ? 'selected' : '' ?>>Online</option>
			<option value="offline" <?= $mode === 'offline' ? 'selected' : '' ?>>Offline</option>
		</select>
	</div>
	<button type="submit" class="btn-primary">Terapkan</button>
	<a href="<?= site_url('katalog/program/'.(int) $program->id) ?>" class="btn-ghost">Reset</a>
</form>

<?php if (! $kelas): ?>
	<div class="card p-8 text-center text-sm text-muted">Belum ada kelas aktif untuk filter ini.</div>
<?php else: ?>
	<p class="mb-3 text-sm text-muted"><?= count($kelas) ?> kelas ditemukan</p>
	<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ($kelas as $row): ?>
			<div class="card flex flex-col p-5">
				<div class="mb-2 flex items-start justify-between gap-2">
					<span class="font-semibold text-ink"><?= html_escape($row->level_nama) ?></span>
					<span class="badge <?= $row->mode === 'online' ? 'bg-sky-100 text-sky-800' : 'bg-violet-100 text-violet-800' ?>">
						<?= $row->mode === 'online' ? 'Online' : 'Offline' ?>
					</span>
				</div>
				<p class="mb-1 text-sm text-muted"><strong class="text-ink"><?= html_escape($row->cabang_nama) ?></strong> <span class="text-slate-400">(<?= html_escape($row->cabang_wilayah) ?>)</span></p>
				<p class="mb-1 text-sm text-muted">Guru: <?= html_escape($row->guru_nama) ?></p>
				<p class="mb-3 text-sm text-muted">Jadwal: <?= html_escape($row->jadwal) ?> (<?= html_escape($row->zona_waktu) ?>)</p>
				<div class="mb-4 space-y-0.5 text-sm text-muted">
					<p>Biaya pendaftaran: <strong class="text-ink"><?= rupiah($row->biaya_pendaftaran) ?></strong></p>
					<p>Biaya SPP/bulan: <strong class="text-ink"><?= rupiah($row->biaya_spp) ?></strong></p>
				</div>
				<div class="mt-auto flex gap-2">
					<a href="<?= site_url('daftar/registrasi/'.$row->id) ?>" class="btn-primary btn-sm flex-1">Daftar Kelas Ini</a>
					<a href="<?= site_url('katalog/detail/'.$row->id) ?>" class="btn-outline btn-sm">Detail</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
