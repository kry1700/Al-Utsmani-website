<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Peserta Aktif — <?= html_escape($cabang->nama) ?></h1>
		<p class="mt-1 text-sm text-muted"><?= html_escape($cabang->wilayah) ?> &middot; <?= count($detail) ?> peserta aktif</p>
	</div>
	<a href="<?= site_url('admin/perkembangan') ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Perkembangan per Cabang</a>
</div>

<section class="card overflow-hidden">
	<?php if (! $detail): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada peserta aktif di cabang ini.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Peserta</th>
						<th class="th">No. WA</th>
						<th class="th">Kelas Aktif</th>
						<th class="th">Aktif Sejak</th>
						<th class="th">Kehadiran</th>
						<th class="th">Progres Terakhir</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($detail as $pid => $baris): ?>
						<?php $stat = isset($absensi[$pid]) ? $absensi[$pid] : NULL; ?>
						<tr class="hover:bg-canvas">
							<td class="td">
								<a href="<?= site_url('admin/peserta/detail/'.$pid) ?>" class="font-semibold text-ink hover:underline"><?= html_escape($baris['peserta']->nama) ?></a>
								<div class="text-xs text-slate-400"><?= $baris['peserta']->kemampuan_awal === 'hafalan' ? 'Sudah punya hafalan' : 'Pemula' ?></div>
							</td>
							<td class="td text-muted"><?= html_escape($baris['peserta']->no_wa) ?></td>
							<td class="td text-muted"><?= html_escape(implode(', ', $baris['kelas'])) ?></td>
							<td class="td whitespace-nowrap text-muted"><?= html_escape(tanggal_indo($baris['sejak'])) ?></td>
							<td class="td whitespace-nowrap">
								<?php if ($stat): ?>
									<?= (int) $stat->hadir ?>/<?= (int) $stat->total ?> hadir
								<?php else: ?>
									<span class="text-slate-400">Belum ada</span>
								<?php endif; ?>
							</td>
							<td class="td text-muted">
								<?php if ($stat && $stat->pertemuan_nomor !== NULL): ?>
									Pertemuan <?= (int) $stat->pertemuan_nomor ?>
									(<?= html_escape(tanggal_indo($stat->pertemuan_tanggal)) ?>):
									<?php if ($stat->hal_mulai !== NULL || $stat->hal_selesai !== NULL): ?>
										Hal. <?= $stat->hal_mulai !== NULL ? (int) $stat->hal_mulai : '?' ?>&ndash;<?= $stat->hal_selesai !== NULL ? (int) $stat->hal_selesai : '?' ?>
									<?php else: ?>
										<span class="text-slate-400">tanpa catatan halaman</span>
									<?php endif; ?>
								<?php else: ?>
									<span class="text-slate-400">Belum ada</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
