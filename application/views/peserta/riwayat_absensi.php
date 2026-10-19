<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Riwayat Absensi</h1>

<?php $this->load->view('peserta/_pilih_profil', array('profil' => $profil, 'aksi' => 'peserta/riwayat/absensi', 'profilList' => $profilList)); ?>

<section class="card overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Absensi: <?= html_escape($profil->nama) ?></h2>
	</div>
	<?php if (! $riwayat): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada catatan absensi untuk peserta ini.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Tanggal</th>
						<th class="th">Pertemuan</th>
						<th class="th">Kelas</th>
						<th class="th">Status</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($riwayat as $a): ?>
						<tr class="hover:bg-canvas">
							<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($a->pertemuan_tanggal)) ?></td>
							<td class="td">Pertemuan <?= (int) $a->pertemuan_nomor ?></td>
							<td class="td"><?= html_escape(label_kelas((array) $a)) ?></td>
							<td class="td"><?= badge('absensi', $a->status) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
