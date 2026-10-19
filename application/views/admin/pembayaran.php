<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Verifikasi Pembayaran</h1>

<form method="get" action="<?= site_url('admin/pembayaran') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div>
		<label class="label" for="status">Status</label>
		<select id="status" name="status" class="input w-auto">
			<?= pilih_opsi(array('menunggu' => 'Menunggu', 'diverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak', '' => 'Semua'), $status) ?>
		</select>
	</div>
	<div>
		<label class="label" for="jenis">Jenis</label>
		<select id="jenis" name="jenis" class="input w-auto">
			<?= pilih_opsi(array('' => 'Semua', 'pendaftaran' => 'Pendaftaran', 'spp' => 'SPP'), $jenis) ?>
		</select>
	</div>
	<button type="submit" class="btn-primary">Tampilkan</button>
</form>

<?php if (! $pembayaran): ?>
	<div class="card p-8 text-center text-sm text-muted">Tidak ada pembayaran pada filter ini.</div>
<?php else: ?>
	<div class="space-y-4">
		<?php foreach ($pembayaran as $b): ?>
			<div class="card p-5">
				<div class="flex flex-wrap items-start justify-between gap-4">
					<div class="min-w-0">
						<div class="mb-1 flex flex-wrap items-center gap-2">
							<span class="font-semibold text-ink"><?= html_escape($b->peserta_nama) ?></span>
							<?= badge('pembayaran', $b->status) ?>
						</div>
						<p class="text-sm text-muted">
							<?= $b->jenis === 'spp' ? 'SPP '.html_escape(nama_bulan($b->spp_periode)) : 'Pendaftaran' ?>
							&middot; <strong class="font-semibold text-ink"><?= rupiah($b->jumlah) ?></strong>
						</p>
						<?php if ($b->jenis === 'pendaftaran' && $b->kelas_ui_id): ?>
							<p class="text-sm text-muted"><?= html_escape(label_kelas((array) $b)) ?></p>
						<?php endif; ?>
						<p class="mt-1 text-xs text-slate-400">
							Diunggah <?= html_escape(tanggal_indo($b->created_at)) ?>
							<?php if ($b->verified_at): ?>
								&middot; diproses <?= html_escape(tanggal_indo($b->verified_at)) ?><?= $b->verifier_nama ? ' oleh '.html_escape($b->verifier_nama) : '' ?>
							<?php endif; ?>
						</p>
						<?php if ($b->status === 'ditolak' && $b->catatan_admin): ?>
							<p class="mt-2 rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700">Catatan: <?= html_escape($b->catatan_admin) ?></p>
						<?php endif; ?>
						<p class="mt-3">
							<a href="<?= site_url('admin/pembayaran/bukti/'.$b->id) ?>" target="_blank" rel="noopener" class="text-sm font-semibold text-primary hover:underline">Lihat bukti pembayaran &rarr;</a>
						</p>
					</div>

					<?php if ($b->status === 'menunggu'): ?>
						<div class="flex w-full flex-col gap-2 sm:w-56">
							<?= form_open('admin/pembayaran/verifikasi/'.$b->id) ?>
								<button type="submit" class="btn-primary w-full">Verifikasi</button>
							<?= form_close() ?>

							<?= form_open('admin/pembayaran/tolak/'.$b->id, array('class' => 'space-y-2')) ?>
								<input type="text" name="catatan_admin" maxlength="255" required placeholder="Alasan penolakan (wajib)" class="input">
								<button type="submit" class="w-full rounded-full border border-rose-200 bg-rose-50 px-5 py-2.5 text-sm font-semibold text-rose-700 transition-colors hover:bg-rose-100">Tolak</button>
							<?= form_close() ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
