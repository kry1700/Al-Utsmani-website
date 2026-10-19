<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<h1 class="page-title"><?= html_escape($peserta->nama) ?></h1>
	<div class="flex items-center gap-2">
		<a href="<?= site_url('admin/peserta/edit/'.$peserta->id) ?>" class="btn-outline btn-sm">Edit Data</a>
		<a href="<?= site_url('admin/peserta') ?>" class="btn-ghost btn-sm">&larr; Daftar Peserta</a>
	</div>
</div>

<div class="grid gap-5 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Profil Peserta</h2></div>
			<div class="card-body">
				<dl class="divide-y divide-line text-sm">
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Level / Rank</dt>
						<dd class="flex items-center gap-2">
							<?= badge_level($peserta->level_nama, $peserta->level_urutan) ?>
							<a href="<?= site_url('admin/penempatan/form/'.$peserta->id) ?>" class="text-xs font-semibold text-primary hover:underline"><?= $peserta->level_id === NULL ? 'Tetapkan level' : 'Ubah' ?></a>
						</dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Jenis Kelamin</dt>
						<dd><?= $peserta->jenis_kelamin === 'L' ? 'Laki-laki' : ($peserta->jenis_kelamin === 'P' ? 'Perempuan' : '-') ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Tanggal Lahir</dt>
						<dd><?= html_escape(tanggal_indo($peserta->tanggal_lahir)) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">No. WhatsApp</dt>
						<dd><?= html_escape($peserta->no_wa) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Nama Wali</dt>
						<dd><?= html_escape($peserta->nama_wali) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Kemampuan Awal</dt>
						<dd><?= $peserta->kemampuan_awal === 'hafalan' ? 'Sudah punya hafalan' : 'Pemula' ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Alamat</dt>
						<dd><?= html_escape($peserta->alamat) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Terdaftar Sejak</dt>
						<dd><?= html_escape(tanggal_indo($peserta->created_at)) ?></dd></div>
				</dl>
			</div>
		</section>

		<section class="card overflow-hidden">
			<div class="card-head"><h2 class="font-semibold text-ink">Riwayat Pendaftaran</h2></div>
			<?php if (! $pendaftaran): ?>
				<div class="p-5 text-sm text-muted">Belum ada pendaftaran kelas.</div>
			<?php else: ?>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-line">
						<thead class="bg-slate-50">
							<tr>
								<th class="th">Kelas</th>
								<th class="th">Guru</th>
								<th class="th">Tanggal</th>
								<th class="th">Status</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-line">
							<?php foreach ($pendaftaran as $pd): ?>
								<tr class="hover:bg-canvas">
									<td class="td"><?= html_escape(label_kelas((array) $pd)) ?></td>
									<td class="td text-muted"><?= html_escape($pd->guru_nama) ?></td>
									<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($pd->created_at)) ?></td>
									<td class="td"><?= badge('pendaftaran', $pd->status) ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</section>

		<section class="card overflow-hidden">
			<div class="card-head"><h2 class="font-semibold text-ink">Riwayat Pembayaran</h2></div>
			<?php if (! $pembayaran): ?>
				<div class="p-5 text-sm text-muted">Belum ada pembayaran.</div>
			<?php else: ?>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-line">
						<thead class="bg-slate-50">
							<tr>
								<th class="th">Tanggal</th>
								<th class="th">Jenis</th>
								<th class="th">Jumlah</th>
								<th class="th">Status</th>
								<th class="th">Bukti</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-line">
							<?php foreach ($pembayaran as $b): ?>
								<tr class="hover:bg-canvas">
									<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($b->created_at)) ?></td>
									<td class="td"><?= $b->jenis === 'spp' ? 'SPP '.html_escape(nama_bulan($b->spp_periode)) : 'Pendaftaran' ?></td>
									<td class="td whitespace-nowrap"><?= rupiah($b->jumlah) ?></td>
									<td class="td"><?= badge('pembayaran', $b->status) ?></td>
									<td class="td"><a href="<?= site_url('admin/pembayaran/bukti/'.$b->id) ?>" target="_blank" rel="noopener" class="text-sm font-semibold text-primary hover:underline">Lihat</a></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</section>
	</div>

	<div class="space-y-5">
		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Akun Login</h2></div>
			<div class="card-body">
				<dl class="space-y-1 text-sm">
					<div><span class="text-muted">Username:</span> <?= html_escape($akun->username) ?></div>
					<div><span class="text-muted">Email:</span> <?= html_escape($akun->email) ?></div>
					<div class="flex items-center gap-2"><span class="text-muted">Status:</span> <?= badge('status', $akun->status) ?></div>
				</dl>
				<?= form_open('admin/peserta/setStatus/'.$peserta->id, array('class' => 'mt-4')) ?>
					<button type="submit" class="w-full rounded-full px-5 py-2.5 text-sm font-semibold transition-colors <?= $akun->status === 'aktif' ? 'border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border border-emerald-200 bg-primary-soft text-primary hover:bg-emerald-100' ?>">
						<?= $akun->status === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?>
					</button>
				<?= form_close() ?>
			</div>
		</section>

		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Peserta Lain di Akun Ini</h2></div>
			<ul class="divide-y divide-line px-5 text-sm">
				<?php foreach ($keluarga as $k): ?>
					<li class="flex items-center justify-between gap-2 py-2.5">
						<span class="<?= (int) $k->id === (int) $peserta->id ? 'font-semibold text-ink' : 'text-muted' ?>"><?= html_escape($k->nama) ?></span>
						<?php if ((int) $k->id !== (int) $peserta->id): ?>
							<a href="<?= site_url('admin/peserta/detail/'.$k->id) ?>" class="text-xs font-semibold text-primary hover:underline">Buka</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="card">
			<div class="card-head">
				<h2 class="font-semibold text-ink">Riwayat Level</h2>
				<a href="<?= site_url('admin/penempatan/form/'.$peserta->id) ?>" class="text-xs font-semibold text-primary hover:underline">Tetapkan</a>
			</div>
			<?php if (! $riwayatLevel): ?>
				<p class="card-body text-sm text-muted">Belum ditempatkan. Peserta baru menunggu proses penempatan level.</p>
			<?php else: ?>
				<ul class="divide-y divide-line px-5 text-sm">
					<?php foreach ($riwayatLevel as $rl): ?>
						<li class="py-2.5">
							<div class="flex items-center justify-between gap-2">
								<?= badge_level($rl->level_nama, $rl->level_urutan) ?>
								<span class="text-xs text-slate-400"><?= html_escape(tanggal_indo($rl->created_at)) ?></span>
							</div>
							<?php if (! empty($rl->catatan)): ?>
								<p class="mt-1 text-xs text-muted"><?= html_escape($rl->catatan) ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>

		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Tagihan SPP Terakhir</h2></div>
			<?php if (! $tagihan): ?>
				<p class="card-body text-sm text-muted">Belum ada tagihan.</p>
			<?php else: ?>
				<ul class="divide-y divide-line px-5 text-sm">
					<?php foreach (array_slice($tagihan, 0, 6) as $t): ?>
						<li class="flex items-center justify-between gap-2 py-2.5">
							<span class="text-muted"><?= html_escape(nama_bulan($t->periode)) ?></span>
							<span class="flex items-center gap-2">
								<span class="font-medium text-ink"><?= rupiah($t->nominal) ?></span>
								<?= badge('tagihan', $t->status) ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="px-5 pb-5 pt-1"><a href="<?= site_url('admin/tagihan?periode='.$tagihan[0]->periode) ?>" class="text-sm font-semibold text-primary hover:underline">Lihat semua tagihan &rarr;</a></div>
			<?php endif; ?>
		</section>
	</div>
</div>
