<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<h1 class="page-title">Penempatan Level — <?= html_escape($peserta->nama) ?></h1>
	<a href="<?= site_url('admin/penempatan') ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Daftar Penempatan</a>
</div>

<div class="grid gap-5 lg:grid-cols-2">
	<div class="space-y-5">
		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Data Peserta</h2></div>
			<div class="card-body">
				<dl class="divide-y divide-line text-sm">
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Level Saat Ini</dt>
						<dd><?= badge_level($peserta->level_nama, $peserta->level_urutan) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Ditetapkan Pada</dt>
						<dd><?= html_escape(tanggal_indo($peserta->level_ditetapkan_at)) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Kemampuan Awal</dt>
						<dd><?= $peserta->kemampuan_awal === 'hafalan' ? 'Sudah punya hafalan' : 'Pemula' ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">No. WhatsApp</dt>
						<dd><?= html_escape($peserta->no_wa) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Nama Wali</dt>
						<dd><?= html_escape($peserta->nama_wali) ?></dd></div>
				</dl>
				<a href="<?= site_url('admin/peserta/detail/'.$peserta->id) ?>" class="mt-4 inline-block text-sm font-semibold text-primary hover:underline">Buka detail peserta &rarr;</a>
			</div>
		</section>

		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Tetapkan Level</h2></div>
			<div class="card-body">
				<?php if (validation_errors()): ?>
					<div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= validation_errors() ?></div>
				<?php endif; ?>
				<?= form_open('admin/penempatan/simpan/'.$peserta->id, array('class' => 'space-y-4')) ?>
					<div>
						<label class="label" for="level_id">Level</label>
						<select id="level_id" name="level_id" required class="input">
							<option value="">— Pilih level —</option>
							<?php foreach ($levelList as $lv): ?>
								<option value="<?= (int) $lv->id ?>"<?= (int) $peserta->level_id === (int) $lv->id ? ' selected' : '' ?>><?= html_escape($lv->nama) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label class="label" for="catatan">Catatan Penilaian (opsional)</label>
						<textarea id="catatan" name="catatan" rows="3" maxlength="255" class="input" placeholder="Mis. hasil tes baca, tingkat kelancaran, dsb."><?= set_value('catatan') ?></textarea>
					</div>
					<button type="submit" class="btn-primary">Simpan Penempatan</button>
				<?= form_close() ?>
			</div>
		</section>
	</div>

	<section class="card overflow-hidden">
		<div class="card-head"><h2 class="font-semibold text-ink">Riwayat Penempatan</h2></div>
		<?php if (! $riwayat): ?>
			<div class="p-5 text-sm text-muted">Belum ada riwayat penempatan level.</div>
		<?php else: ?>
			<ol class="divide-y divide-line">
				<?php foreach ($riwayat as $r): ?>
					<li class="px-5 py-4 text-sm">
						<div class="flex flex-wrap items-center justify-between gap-2">
							<span><?= badge_level($r->level_nama, $r->level_urutan) ?></span>
							<span class="text-xs text-slate-400"><?= html_escape(tanggal_indo($r->created_at)) ?></span>
						</div>
						<div class="mt-1 text-xs text-muted">Ditetapkan oleh <?= html_escape($r->penilai_nama) ?></div>
						<?php if (! empty($r->catatan)): ?>
							<p class="mt-1 text-muted"><?= html_escape($r->catatan) ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</section>
</div>
