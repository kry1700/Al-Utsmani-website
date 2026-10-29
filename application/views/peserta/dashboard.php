<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Dashboard</h1>

<section class="mb-6 grid gap-4 sm:grid-cols-3">
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Kelas Aktif Diikuti</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $jumlahAktif ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Tagihan SPP Belum Lunas</div>
		<div class="mt-1 text-2xl font-bold <?= $tagihan->jumlah > 0 ? 'text-amber-600' : 'text-primary' ?>"><?= (int) $tagihan->jumlah ?></div>
		<?php if ($tagihan->jumlah > 0): ?>
			<div class="mt-1 text-xs text-muted">Total <?= rupiah($tagihan->total) ?></div>
		<?php endif; ?>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Kehadiran</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $kehadiran['hadir'] ?>/<?= (int) $kehadiran['total'] ?></div>
		<div class="mt-1 text-xs text-muted">hadir / total pertemuan</div>
	</div>
</section>

<section class="card mb-6 overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Profil Singkat</h2>
		<a href="<?= site_url('daftar') ?>" class="text-sm font-semibold text-primary hover:underline">+ Daftar Kelas Baru</a>
	</div>
	<div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ($profilList as $p): $r = isset($ringkas[(int) $p->id]) ? $ringkas[(int) $p->id] : array('jumlah' => 0, 'program_kode' => NULL, 'cabang_nama' => NULL); ?>
			<div class="rounded-xl border border-line p-4">
				<div class="flex items-start justify-between gap-2">
					<h3 class="font-semibold text-ink"><?= html_escape($p->nama) ?></h3>
					<?= badge_level($p->level_nama, $p->level_urutan) ?>
				</div>
				<p class="mt-2 text-sm text-muted">
					<?php if ($r['program_kode'] !== NULL): ?>
						<?= html_escape($r['program_kode']) ?> · <?= html_escape($r['cabang_nama']) ?>
						<span class="text-slate-400">(<?= (int) $r['jumlah'] ?> kelas aktif)</span>
					<?php else: ?>
						<span class="text-slate-400">Belum ada kelas aktif</span>
					<?php endif; ?>
				</p>
				<?php if ($p->level_id === NULL): ?>
					<p class="mt-2 text-xs text-amber-700">Level belum ditempatkan. Pengelola akan menetapkan level setelah proses penempatan/tes.</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="card mb-6 overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Kelas Saya</h2>
	</div>
	<?php if (! $pendaftaran): ?>
		<div class="p-5 text-sm text-muted">
			Belum ada pendaftaran. <a href="<?= site_url('daftar') ?>" class="font-semibold text-primary hover:underline">Cari kelas &amp; daftar</a>.
		</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Peserta</th>
						<th class="th">Kelas</th>
						<th class="th">Jadwal</th>
						<th class="th">Status</th>
						<th class="th">Kelas Online</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($pendaftaran as $pd): ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink"><?= html_escape($pd->peserta_nama) ?></td>
							<td class="td">
								<?= html_escape(label_kelas((array) $pd)) ?>
								<div class="text-xs text-muted">Guru: <?= html_escape($pd->guru_nama) ?></div>
							</td>
							<td class="td text-muted">
								<?= html_escape(jadwal_tampil($pd)) ?>
							</td>
							<td class="td"><?= badge('pendaftaran', $pd->status) ?></td>
							<td class="td">
								<?php if ($pd->mode === 'online' && $pd->status === 'aktif' && $pd->link_meeting): ?>
									<?php if (filter_var($pd->link_meeting, FILTER_VALIDATE_URL)): ?>
										<a href="<?= html_escape($pd->link_meeting) ?>" target="_blank" rel="noopener" class="font-semibold text-sky-700 hover:underline">Buka Link</a>
									<?php else: ?>
										<span class="text-ink"><?= html_escape($pd->link_meeting) ?></span>
									<?php endif; ?>
								<?php else: ?>
									<span class="text-slate-400">&mdash;</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>

<section class="card overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Tagihan SPP Belum Lunas</h2>
		<a href="<?= site_url('peserta/pembayaran') ?>" class="text-sm font-semibold text-primary hover:underline">Buka Pembayaran &amp; SPP</a>
	</div>
	<?php if (! $tagihanTerbaru): ?>
		<div class="p-5 text-sm text-muted">Tidak ada tagihan yang menunggu pembayaran.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Peserta</th>
						<th class="th">Periode</th>
						<th class="th">Nominal</th>
						<th class="th">Jatuh Tempo</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($tagihanTerbaru as $t): ?>
						<tr class="hover:bg-canvas">
							<td class="td"><?= html_escape($t->peserta_nama) ?></td>
							<td class="td"><?= html_escape(nama_bulan($t->periode)) ?></td>
							<td class="td whitespace-nowrap"><?= rupiah($t->nominal) ?></td>
							<td class="td <?= $t->jatuh_tempo < date('Y-m-d') ? 'font-medium text-rose-600' : 'text-muted' ?>">
								<?= html_escape(tanggal_indo($t->jatuh_tempo)) ?>
								<?= $t->jatuh_tempo < date('Y-m-d') ? ' (terlambat)' : '' ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
