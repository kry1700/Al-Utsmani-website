<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Pendaftaran Kelas</h1>
	<a href="<?= site_url('katalog') ?>" class="btn-primary">Telusuri Katalog</a>
</div>

<?php if (! $pendaftaran): ?>
	<div class="card p-8 text-center text-sm text-muted">
		Belum ada pendaftaran. Pilih kelas terlebih dahulu di <a href="<?= site_url('katalog') ?>" class="font-semibold text-primary hover:underline">Katalog Kelas</a>.
	</div>
<?php else: ?>
	<div class="space-y-4">
		<?php foreach ($pendaftaran as $pd): ?>
			<?php
			$bayar = isset($bayarTerakhir[(int) $pd->id]) ? $bayarTerakhir[(int) $pd->id] : NULL;
			$perluBukti = ($pd->status === 'menunggu' && (int) $pd->biaya_pendaftaran > 0);
			?>
			<div class="card p-5">
				<div class="mb-3 flex flex-wrap items-start justify-between gap-3">
					<div>
						<h2 class="font-semibold text-ink"><?= html_escape($pd->peserta_nama) ?></h2>
						<p class="text-sm text-muted"><?= html_escape(label_kelas((array) $pd)) ?></p>
						<p class="text-sm text-muted">
							Guru: <?= html_escape($pd->guru_nama) ?> &middot;
							Jadwal: <?= html_escape(jadwal_tampil($pd)) ?>
						</p>
						<p class="text-xs text-slate-400">Didaftarkan <?= html_escape(tanggal_indo($pd->created_at)) ?></p>
					</div>
					<div class="text-right">
						<div><?= badge('pendaftaran', $pd->status) ?></div>
						<div class="mt-1 text-sm text-muted">Biaya: <strong class="text-ink"><?= rupiah($pd->biaya_pendaftaran) ?></strong></div>
					</div>
				</div>

				<?php if ($perluBukti): ?>
					<div class="mt-3 border-t border-line pt-4">
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
							<?= form_open_multipart('peserta/pendaftaran/upload/'.$pd->id, array('class' => 'mt-3 flex flex-wrap items-end gap-3')) ?>
								<div>
									<label class="label" for="bukti_<?= (int) $pd->id ?>">File bukti (JPG/PNG/PDF, maks 2 MB)</label>
									<input type="file" id="bukti_<?= (int) $pd->id ?>" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required
										class="rounded-xl border border-line bg-white px-3 py-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-primary-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary">
								</div>
								<button type="submit" class="btn-primary">Unggah</button>
							<?= form_close() ?>
						</details>
					</div>
				<?php elseif ($pd->status === 'aktif'): ?>
					<p class="mt-3 border-t border-line pt-4 text-sm text-primary">
						Pendaftaran aktif. Silakan lihat jadwal dan link kelas di Dashboard.
					</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
