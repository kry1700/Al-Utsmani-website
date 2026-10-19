<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Riwayat Absensi — Pertemuan <?= (int) $pertemuan->nomor ?></h1>
		<p class="mt-1 text-sm text-muted">
			<?= html_escape(label_kelas((array) $pertemuan)) ?> &middot;
			<?= html_escape(tanggal_indo($pertemuan->tanggal)) ?>
		</p>
	</div>
	<a href="<?= site_url('guru/absensi/isi/'.$pertemuan->id) ?>" class="btn-outline btn-sm">Ubah isian</a>
</div>

<section class="card overflow-hidden">
	<?php if (! $isian): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada absensi untuk pertemuan ini.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Peserta</th>
						<th class="th">Kehadiran</th>
						<th class="th">Halaman</th>
						<th class="th">Catatan Guru</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($isian as $a): ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink"><?= html_escape($a->peserta_nama) ?></td>
							<td class="td"><?= badge('absensi', $a->status) ?></td>
							<td class="td whitespace-nowrap">
								<?php if ($a->hal_mulai !== NULL || $a->hal_selesai !== NULL): ?>
									Hal. <?= $a->hal_mulai !== NULL ? (int) $a->hal_mulai : '?' ?>
									&ndash;
									<?= $a->hal_selesai !== NULL ? (int) $a->hal_selesai : '?' ?>
								<?php else: ?>
									<span class="text-slate-400">&mdash;</span>
								<?php endif; ?>
							</td>
							<td class="td text-muted"><?= html_escape($a->catatan_guru) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
