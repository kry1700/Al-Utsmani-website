<?php defined('BASEPATH') OR exit('No direct script access allowed');

$perWilayah = array();
foreach ($cabangs as $c) {
	$perWilayah[$c->wilayah][] = $c;
}
$judul = array();
if ($programTerpilih) { $judul[] = $programTerpilih->kode; }
if ($cabangTerpilih) { $judul[] = $cabangTerpilih->nama; }
if ($mode !== '') { $judul[] = ucfirst($mode); }
?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Pilih Kelas</h1>
		<p class="mt-1 text-sm text-muted"><?= $judul ? html_escape(implode(' · ', $judul)) : 'Semua kelas aktif' ?></p>
	</div>
	<a href="<?= site_url('daftar') ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Ubah cabang/program</a>
</div>

<form method="get" action="<?= site_url('daftar/kelas') ?>" class="card mb-6 grid items-end gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4">
	<div>
		<label class="label" for="f-cabang">Cabang</label>
		<select id="f-cabang" name="cabang" class="input">
			<option value="">Semua cabang</option>
			<?php foreach ($perWilayah as $wilayah => $daftar): ?>
				<optgroup label="<?= html_escape($wilayah) ?>">
					<?php foreach ($daftar as $c): ?>
						<option value="<?= (int) $c->id ?>" <?= $cabangTerpilih && (int) $cabangTerpilih->id === (int) $c->id ? 'selected' : '' ?>><?= html_escape($c->nama) ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select>
	</div>
	<div>
		<label class="label" for="f-program">Program</label>
		<select id="f-program" name="program" class="input">
			<option value="">Semua program</option>
			<?php foreach ($programs as $p): ?>
				<option value="<?= (int) $p->id ?>" <?= $programTerpilih && (int) $programTerpilih->id === (int) $p->id ? 'selected' : '' ?>><?= html_escape($p->kode) ?> — <?= html_escape($p->nama) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div>
		<label class="label" for="f-mode">Mode</label>
		<select id="f-mode" name="mode" class="input">
			<option value="">Online &amp; Offline</option>
			<option value="online" <?= $mode === 'online' ? 'selected' : '' ?>>Online</option>
			<option value="offline" <?= $mode === 'offline' ? 'selected' : '' ?>>Offline</option>
		</select>
	</div>
	<button type="submit" class="btn-primary">Terapkan</button>
</form>

<?php if (! $kelas): ?>
	<div class="card p-8 text-center text-sm text-muted">
		Belum ada kelas aktif yang cocok. Coba ubah cabang/program/mode.
	</div>
<?php else: ?>
	<p class="mb-3 text-sm text-muted"><?= count($kelas) ?> kelas ditemukan — pilih salah satu untuk melanjutkan registrasi.</p>
	<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ($kelas as $row): ?>
			<div class="card flex flex-col p-5">
				<div class="mb-2 flex items-start justify-between gap-2">
					<div>
						<span class="badge bg-primary-soft text-primary"><?= html_escape($row->program_kode) ?></span>
						<span class="ml-1 text-sm font-semibold text-ink"><?= html_escape($row->level_nama) ?></span>
					</div>
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
				<a href="<?= site_url('daftar/registrasi/'.$row->id) ?>" class="btn-primary mt-auto">
					Daftar Kelas Ini
				</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
