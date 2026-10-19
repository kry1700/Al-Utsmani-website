<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Penempatan Level</h1>
		<p class="mt-1 text-sm text-muted">Level peserta ditetapkan oleh pengelola (bukan dipilih peserta) dan tampil sebagai title di profilnya.</p>
	</div>
	<?php if ((int) $jumlahBelum > 0): ?>
		<a href="<?= site_url('admin/penempatan?level=belum') ?>" class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-5 py-2.5 text-sm font-semibold text-amber-800 transition-colors hover:bg-amber-200">
			<?= (int) $jumlahBelum ?> peserta belum ditempatkan
		</a>
	<?php endif; ?>
</div>

<form method="get" action="<?= site_url('admin/penempatan') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div class="min-w-48 flex-1">
		<label class="label" for="q">Cari nama / no. WhatsApp</label>
		<input type="text" id="q" name="q" value="<?= html_escape($q) ?>" class="input">
	</div>
	<div class="min-w-48">
		<label class="label" for="level">Level</label>
		<select id="level" name="level" class="input">
			<option value="">Semua level</option>
			<option value="belum"<?= $levelFilter === 'belum' ? ' selected' : '' ?>>Belum ditempatkan</option>
			<?php foreach ($levelList as $lv): ?>
				<option value="<?= (int) $lv->id ?>"<?= (string) $levelFilter === (string) $lv->id ? ' selected' : '' ?>><?= html_escape($lv->nama) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<button type="submit" class="btn-primary">Terapkan</button>
	<?php if ($q !== '' || $levelFilter !== ''): ?>
		<a href="<?= site_url('admin/penempatan') ?>" class="btn-ghost">Reset</a>
	<?php endif; ?>
</form>

<section class="card overflow-hidden">
	<?php if (! $peserta): ?>
		<div class="p-8 text-center text-sm text-muted">Tidak ada peserta<?= $levelFilter === 'belum' ? ' yang belum ditempatkan' : '' ?>.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Nama</th>
						<th class="th">Akun</th>
						<th class="th">Kemampuan Awal</th>
						<th class="th">Level Saat Ini</th>
						<th class="th">Kelas Aktif</th>
						<th class="th">Status Akun</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($peserta as $p): ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink"><?= html_escape($p->nama) ?></td>
							<td class="td text-muted">
								<?= html_escape($p->username) ?>
								<div class="text-xs text-slate-400"><?= html_escape($p->email) ?></div>
							</td>
							<td class="td text-muted"><?= $p->kemampuan_awal === 'hafalan' ? 'Hafalan' : 'Pemula' ?></td>
							<td class="td"><?= badge_level($p->level_nama, $p->level_urutan) ?></td>
							<td class="td"><?= (int) $p->pendaftaran_aktif ?></td>
							<td class="td"><?= badge('status', $p->user_status) ?></td>
							<td class="td text-right whitespace-nowrap">
								<a href="<?= site_url('admin/penempatan/form/'.$p->id) ?>" class="text-sm font-semibold text-primary hover:underline"><?= $p->level_id === NULL ? 'Tetapkan Level' : 'Ubah Level' ?></a>
								<span class="mx-1 text-slate-300">|</span>
								<a href="<?= site_url('admin/peserta/detail/'.$p->id) ?>" class="text-sm text-muted hover:text-primary hover:underline">Detail</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
