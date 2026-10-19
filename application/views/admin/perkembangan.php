<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-6">
	<h1 class="page-title">Perkembangan per Cabang</h1>
	<p class="mt-1 text-sm text-muted">Rekap realtime — pemasukan dihitung dari pembayaran terverifikasi bulan <?= html_escape(nama_bulan($periode)) ?>.</p>
</div>

<section class="card overflow-hidden">
	<?php if (! $cabangList): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada data cabang.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Cabang</th>
						<th class="th text-right">Peserta Aktif</th>
						<th class="th text-right">Program Aktif</th>
						<th class="th text-right">Kelas Aktif</th>
						<th class="th text-right">Guru</th>
						<th class="th text-right">Pemasukan Bulan Ini</th>
						<th class="th text-right">Tunggakan</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($cabangList as $c): ?>
						<?php
						$cid = (int) $c->id;
						$tunggakan = isset($tunggakanPerCabang[$cid]) ? $tunggakanPerCabang[$cid] : NULL;
						?>
						<tr class="<?= $c->status !== 'aktif' ? 'bg-slate-50 text-muted' : 'hover:bg-canvas' ?>">
							<td class="td">
								<div class="font-semibold <?= $c->status !== 'aktif' ? '' : 'text-ink' ?>"><?= html_escape($c->nama) ?></div>
								<div class="text-xs text-slate-400"><?= html_escape($c->wilayah) ?><?= $c->status !== 'aktif' ? ' — nonaktif' : '' ?></div>
							</td>
							<td class="td text-right"><?= isset($pesertaPerCabang[$cid]) ? (int) $pesertaPerCabang[$cid] : 0 ?></td>
							<td class="td text-right"><?= isset($programPerCabang[$cid]) ? (int) $programPerCabang[$cid] : 0 ?></td>
							<td class="td text-right"><?= isset($kelasPerCabang[$cid]) ? (int) $kelasPerCabang[$cid] : 0 ?></td>
							<td class="td text-right"><?= isset($guruPerCabang[$cid]) ? (int) $guruPerCabang[$cid] : 0 ?></td>
							<td class="td whitespace-nowrap text-right"><?= rupiah(isset($pemasukanPerCabang[$cid]) ? $pemasukanPerCabang[$cid] : 0) ?></td>
							<td class="td whitespace-nowrap text-right <?= $tunggakan && $tunggakan->jumlah > 0 ? 'font-medium text-rose-600' : '' ?>">
								<?= rupiah($tunggakan ? $tunggakan->total : 0) ?>
								<?php if ($tunggakan && $tunggakan->jumlah > 0): ?>
									<div class="text-xs font-normal text-muted"><?= (int) $tunggakan->jumlah ?> tagihan</div>
								<?php endif; ?>
							</td>
							<td class="td whitespace-nowrap text-right">
								<a href="<?= site_url('admin/perkembangan/detail/'.$cid) ?>" class="text-sm font-semibold text-primary hover:underline">Detail peserta</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
