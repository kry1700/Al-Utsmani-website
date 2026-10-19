<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Tagihan SPP</h1>
	<a href="<?= site_url('admin/tagihan/buat?periode='.$periode) ?>" class="btn-primary">Buat Tagihan Massal</a>
</div>

<form method="get" action="<?= site_url('admin/tagihan') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div>
		<label class="label" for="periode">Periode</label>
		<input type="month" id="periode" name="periode" value="<?= html_escape($periode) ?>" class="input w-auto">
	</div>
	<div>
		<label class="label" for="status">Status</label>
		<select id="status" name="status" class="input w-auto">
			<?= pilih_opsi(array('' => 'Semua', 'belum' => 'Belum Lunas', 'lunas' => 'Lunas', 'tunggakan' => 'Tunggakan (lewat tempo)'), $status) ?>
		</select>
	</div>
	<button type="submit" class="btn-primary">Tampilkan</button>
</form>

<section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Total Tagihan</div>
		<div class="mt-1 text-2xl font-bold text-ink"><?= (int) $ringkasan->total ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Lunas</div>
		<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $ringkasan->lunas ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Belum Lunas</div>
		<div class="mt-1 text-2xl font-bold text-amber-600"><?= (int) $ringkasan->belum ?></div>
	</div>
	<div class="stat-card">
		<div class="text-xs font-medium uppercase tracking-wide text-muted">Tunggakan</div>
		<div class="mt-1 text-2xl font-bold <?= $ringkasan->tunggakan_jumlah > 0 ? 'text-rose-600' : 'text-primary' ?>"><?= rupiah($ringkasan->tunggakan_total) ?></div>
		<div class="mt-1 text-xs text-muted"><?= (int) $ringkasan->tunggakan_jumlah ?> tagihan</div>
	</div>
</section>

<section class="card overflow-hidden">
	<?php if (! $tagihan): ?>
		<div class="p-8 text-center text-sm text-muted">Tidak ada tagihan pada periode/filter ini.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Peserta</th>
						<th class="th">Periode</th>
						<th class="th">Nominal</th>
						<th class="th">Jatuh Tempo</th>
						<th class="th">Status</th>
						<th class="th">Lunas</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($tagihan as $t): ?>
						<?php $terlambat = ($t->status === 'belum' && $t->jatuh_tempo < date('Y-m-d')); ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink">
								<a href="<?= site_url('admin/peserta/detail/'.$t->peserta_id) ?>" class="hover:underline"><?= html_escape($t->peserta_nama) ?></a>
							</td>
							<td class="td text-muted"><?= html_escape(nama_bulan($t->periode)) ?></td>
							<td class="td whitespace-nowrap"><?= rupiah($t->nominal) ?></td>
							<td class="td whitespace-nowrap <?= $terlambat ? 'font-medium text-rose-600' : 'text-muted' ?>">
								<?= html_escape(tanggal_indo($t->jatuh_tempo)) ?><?= $terlambat ? ' (terlambat)' : '' ?>
							</td>
							<td class="td"><?= badge('tagihan', $t->status) ?></td>
							<td class="td text-muted"><?= $t->lunas_at ? html_escape(tanggal_indo($t->lunas_at)) : '&mdash;' ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
