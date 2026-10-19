<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Riwayat Progres Belajar</h1>

<?php $this->load->view('peserta/_pilih_profil', array('profil' => $profil, 'aksi' => 'peserta/riwayat/progres', 'profilList' => $profilList)); ?>

<section class="card overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Progres: <?= html_escape($profil->nama) ?></h2>
	</div>
	<?php if (! $riwayat): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada catatan progres untuk peserta ini.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Tanggal</th>
						<th class="th">Kelas</th>
						<th class="th">Pertemuan</th>
						<th class="th">Halaman</th>
						<th class="th">Kehadiran</th>
						<th class="th">Catatan Guru</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($riwayat as $a): ?>
						<tr class="hover:bg-canvas">
							<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($a->pertemuan_tanggal)) ?></td>
							<td class="td"><?= html_escape(label_kelas((array) $a)) ?></td>
							<td class="td">Ke-<?= (int) $a->pertemuan_nomor ?></td>
							<td class="td whitespace-nowrap">
								<?php if ($a->hal_mulai !== NULL || $a->hal_selesai !== NULL): ?>
									Hal. <?= $a->hal_mulai !== NULL ? (int) $a->hal_mulai : '?' ?>
									&ndash;
									<?= $a->hal_selesai !== NULL ? (int) $a->hal_selesai : '?' ?>
								<?php else: ?>
									<span class="text-slate-400">&mdash;</span>
								<?php endif; ?>
							</td>
							<td class="td"><?= badge('absensi', $a->status) ?></td>
							<td class="td text-muted"><?= html_escape($a->catatan_guru) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
