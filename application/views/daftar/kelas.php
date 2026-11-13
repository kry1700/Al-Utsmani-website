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
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Pendaftaran</p>
			<h1 class="au-home-heading au-home-heading--h1">Pilih Kelas</h1>
			<p class="au-home-body au-home-body--lg"><?= $judul ? html_escape(implode(' · ', $judul)) : 'Semua kelas aktif' ?></p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('daftar') ?>" class="au-home-button au-home-button-secondary">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
				Ubah cabang/program
			</a>
		</div>
	</header>

	<form method="get" action="<?= site_url('daftar/kelas') ?>" class="au-filter-bar">
		<div class="au-field">
			<label class="au-label" for="f-cabang">Cabang</label>
			<select id="f-cabang" name="cabang" class="au-select">
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
		<div class="au-field">
			<label class="au-label" for="f-program">Program</label>
			<select id="f-program" name="program" class="au-select">
				<option value="">Semua program</option>
				<?php foreach ($programs as $p): ?>
					<option value="<?= (int) $p->id ?>" <?= $programTerpilih && (int) $programTerpilih->id === (int) $p->id ? 'selected' : '' ?>><?= html_escape($p->kode) ?> — <?= html_escape($p->nama) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="au-field">
			<label class="au-label" for="f-mode">Mode</label>
			<select id="f-mode" name="mode" class="au-select">
				<option value="">Online &amp; Offline</option>
				<option value="online" <?= $mode === 'online' ? 'selected' : '' ?>>Online</option>
				<option value="offline" <?= $mode === 'offline' ? 'selected' : '' ?>>Offline</option>
			</select>
		</div>
		<div class="au-filter-bar__actions">
			<button type="submit" class="au-home-button au-home-button-primary au-home-button--sm">Terapkan</button>
			<a href="<?= site_url('daftar') ?>" class="au-home-button au-home-button-secondary au-home-button--sm">Reset</a>
		</div>
	</form>

	<?php if (! $kelas): ?>
		<div class="au-panel au-empty">
			<h2 class="au-empty__title">Belum ada kelas aktif</h2>
			<p>Tidak ada kelas yang cocok. Coba ubah cabang, program, atau mode.</p>
		</div>
	<?php else: ?>
		<p class="au-home-caption mb-4"><?= count($kelas) ?> kelas ditemukan — pilih salah satu untuk melanjutkan registrasi.</p>
		<div class="au-card-grid">
			<?php foreach ($kelas as $row): ?>
				<article class="au-kelas-card">
					<div class="au-kelas-card__top">
						<div>
							<span class="au-chip au-chip--brand"><?= html_escape($row->program_kode) ?></span>
							<h2 class="au-kelas-card__title mt-2"><?= html_escape($row->level_nama) ?></h2>
						</div>
						<span class="au-chip <?= $row->mode === 'online' ? 'au-chip--online' : 'au-chip--offline' ?>">
							<?= $row->mode === 'online' ? 'Online' : 'Offline' ?>
						</span>
					</div>
					<div class="au-kelas-card__meta">
						<p><strong><?= html_escape($row->cabang_nama) ?></strong> &middot; <?= html_escape($row->cabang_wilayah) ?></p>
						<p>Guru: <strong><?= html_escape($row->guru_nama) ?></strong></p>
						<p>Jadwal: <?= html_escape(function_exists('jadwal_tampil') ? jadwal_tampil($row) : $row->jadwal.' ('.$row->zona_waktu.')') ?></p>
					</div>
					<div class="au-kelas-card__price">
						<span>Pendaftaran<strong><?= rupiah($row->biaya_pendaftaran) ?></strong></span>
						<span>SPP / bulan<strong><?= rupiah($row->biaya_spp) ?></strong></span>
					</div>
					<div class="au-kelas-card__actions">
						<a href="<?= site_url('daftar/registrasi/'.$row->id) ?>" class="au-home-button au-home-button-primary au-home-button--sm">Daftar Kelas Ini</a>
						<a href="<?= site_url('katalog/detail/'.$row->id) ?>" class="au-home-button au-home-button-secondary au-home-button--sm">Detail</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
