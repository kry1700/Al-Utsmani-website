<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-6">
	<h1 class="page-title">Dashboard Guru</h1>
	<p class="mt-1 text-sm text-muted">Assalamu'alaikum, <?= html_escape($user['nama']) ?>.</p>
</div>

<section class="mb-6 grid gap-4 sm:grid-cols-3">
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Kelas Diampu</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $jumlahKelas ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Peserta Aktif</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $jumlahPeserta ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Pertemuan Tercatat</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $jumlahPertemuan ?></div>
	</div>
</section>

<section class="card overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Kelas Saya</h2>
		<a href="<?= site_url('guru/kelas') ?>" class="text-sm font-semibold text-primary hover:underline">Kelola Kelas &rarr;</a>
	</div>
	<?php if (! $kelas): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada kelas yang diampu.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Kelas</th>
						<th class="th">Jadwal</th>
						<th class="th">Peserta</th>
						<th class="th">Status</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($kelas as $k): ?>
						<tr class="hover:bg-canvas">
							<td class="td"><?= html_escape(label_kelas((array) $k)) ?></td>
							<td class="td text-muted"><?= html_escape($k->jadwal) ?> <span class="text-slate-400">(<?= html_escape($k->zona_waktu) ?>)</span></td>
							<td class="td"><?= (int) $k->jumlah_peserta ?></td>
							<td class="td"><?= badge('status', $k->status) ?></td>
							<td class="td text-right">
								<a href="<?= site_url('guru/kelas/detail/'.$k->id) ?>" class="text-sm font-semibold text-primary hover:underline">Detail</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
