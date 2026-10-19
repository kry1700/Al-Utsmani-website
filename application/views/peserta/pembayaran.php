<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Pembayaran dan SPP</h1>

<section class="card mb-6 overflow-hidden">
	<div class="card-head">
		<div>
			<h2 class="font-semibold text-ink">Tagihan SPP</h2>
			<p class="mt-0.5 text-xs text-muted">Pembayaran manual via transfer — unggah bukti, lalu tunggu verifikasi admin.</p>
		</div>
	</div>
	<?php if (! $tagihan): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada tagihan SPP.</div>
	<?php else: ?>
		<div class="divide-y divide-line">
			<?php foreach ($tagihan as $t): ?>
				<?php
				$bayar = isset($bayarPerTagihan[(int) $t->id]) ? $bayarPerTagihan[(int) $t->id] : NULL;
				$terlambat = ($t->status === 'belum' && $t->jatuh_tempo < date('Y-m-d'));
				?>
				<div class="p-5">
					<div class="flex flex-wrap items-start justify-between gap-3">
						<div>
							<p class="font-semibold text-ink"><?= html_escape($t->peserta_nama) ?></p>
							<p class="text-sm text-muted">Periode <?= html_escape(nama_bulan($t->periode)) ?></p>
							<p class="text-xs <?= $terlambat ? 'font-medium text-rose-600' : 'text-muted' ?>">
								Jatuh tempo <?= html_escape(tanggal_indo($t->jatuh_tempo)) ?><?= $terlambat ? ' — terlambat' : '' ?>
							</p>
						</div>
						<div class="text-right">
							<div><?= badge('tagihan', $t->status) ?></div>
							<div class="mt-1 text-sm font-semibold text-ink"><?= rupiah($t->nominal) ?></div>
							<?php if ($t->lunas_at): ?>
								<div class="text-xs text-muted">Lunas <?= html_escape(tanggal_indo($t->lunas_at)) ?></div>
							<?php endif; ?>
						</div>
					</div>

					<?php if ($t->status === 'belum'): ?>
						<div class="mt-4 border-t border-line pt-4">
							<?php if ($bayar): ?>
								<p class="mb-2 text-sm">
									Status bukti terakhir: <?= badge('pembayaran', $bayar->status) ?>
									<a href="<?= site_url('peserta/pembayaran/bukti/'.$bayar->id) ?>" target="_blank" rel="noopener" class="ml-1 font-semibold text-primary hover:underline">Lihat bukti</a>
								</p>
								<?php if ($bayar->status === 'ditolak' && $bayar->catatan_admin): ?>
									<p class="mb-2 rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700">Catatan admin: <?= html_escape($bayar->catatan_admin) ?></p>
								<?php endif; ?>
							<?php else: ?>
								<p class="mb-2 text-sm text-muted">Bukti pembayaran belum diunggah.</p>
							<?php endif; ?>

							<details <?= ($bayar && $bayar->status === 'ditolak') ? 'open' : '' ?>>
								<summary class="cursor-pointer text-sm font-semibold text-primary hover:underline">
									<?= $bayar ? 'Ganti / unggah ulang bukti' : 'Unggah bukti pembayaran' ?>
								</summary>
								<?= form_open_multipart('peserta/pembayaran/uploadTagihan/'.$t->id, array('class' => 'mt-3 flex flex-wrap items-end gap-3')) ?>
									<div>
										<label class="label" for="bukti_<?= (int) $t->id ?>">File bukti (JPG/PNG/PDF, maks 2 MB)</label>
										<input type="file" id="bukti_<?= (int) $t->id ?>" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required
											class="rounded-xl border border-line bg-white px-3 py-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-primary-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary">
									</div>
									<button type="submit" class="btn-primary">Unggah</button>
								<?= form_close() ?>
							</details>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<section class="card overflow-hidden">
	<div class="card-head">
		<h2 class="font-semibold text-ink">Riwayat Pembayaran</h2>
	</div>
	<?php if (! $riwayat): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada riwayat pembayaran.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Tanggal</th>
						<th class="th">Peserta</th>
						<th class="th">Jenis</th>
						<th class="th">Keterangan</th>
						<th class="th">Jumlah</th>
						<th class="th">Status</th>
						<th class="th">Bukti</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($riwayat as $b): ?>
						<tr class="hover:bg-canvas">
							<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($b->created_at)) ?></td>
							<td class="td"><?= html_escape($b->peserta_nama) ?></td>
							<td class="td"><?= $b->jenis === 'spp' ? 'SPP' : 'Pendaftaran' ?></td>
							<td class="td">
								<?php if ($b->jenis === 'spp'): ?>
									<?= html_escape(nama_bulan($b->spp_periode)) ?>
								<?php elseif ($b->kelas_ui_id): ?>
									<?= html_escape(label_kelas((array) $b)) ?>
								<?php else: ?>
									<span class="text-slate-400">&mdash;</span>
								<?php endif; ?>
							</td>
							<td class="td whitespace-nowrap"><?= rupiah($b->jumlah) ?></td>
							<td class="td"><?= badge('pembayaran', $b->status) ?></td>
							<td class="td">
								<a href="<?= site_url('peserta/pembayaran/bukti/'.$b->id) ?>" target="_blank" rel="noopener" class="font-semibold text-primary hover:underline">Lihat</a>
								<?php if ($b->status === 'ditolak' && $b->catatan_admin): ?>
									<div class="mt-1 text-xs text-rose-600"><?= html_escape($b->catatan_admin) ?></div>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
