<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<h1 class="page-title"><?= html_escape(label_kelas((array) $kelas)) ?></h1>
	<a href="<?= site_url('guru/kelas') ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Kembali ke Kelas Saya</a>
</div>

<div class="grid gap-5 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<section class="card">
			<div class="card-head"><h2 class="font-semibold text-ink">Informasi Kelas</h2></div>
			<div class="card-body">
				<dl class="divide-y divide-line text-sm">
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Program</dt>
						<dd><?= html_escape($kelas->program_nama) ?> (<?= html_escape($kelas->program_kode) ?>)</dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Level</dt>
						<dd><?= html_escape($kelas->level_nama) ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Cabang</dt>
						<dd><?= html_escape($kelas->cabang_nama) ?> (<?= html_escape($kelas->cabang_wilayah) ?>)</dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Mode</dt>
						<dd><?= $kelas->mode === 'online' ? 'Online' : 'Offline' ?></dd></div>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Jadwal</dt>
						<dd><?= html_escape($kelas->jadwal) ?> (<?= html_escape($kelas->zona_waktu) ?>)</dd></div>
					<?php if ($kelas->mode === 'online'): ?>
						<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Link Meeting</dt>
							<dd class="break-all">
								<?php if ($kelas->link_meeting): ?>
									<a href="<?= html_escape($kelas->link_meeting) ?>" target="_blank" rel="noopener" class="font-semibold text-sky-700 hover:underline"><?= html_escape($kelas->link_meeting) ?></a>
								<?php else: ?>
									<span class="text-slate-400">Belum diisi admin</span>
								<?php endif; ?>
							</dd></div>
					<?php endif; ?>
				</dl>
			</div>
		</section>

		<section class="card overflow-hidden">
			<div class="card-head">
				<h2 class="font-semibold text-ink">Pertemuan (<?= count($pertemuan) ?>)</h2>
			</div>

			<?= form_open('guru/kelas/tambahPertemuan/'.$kelas->id, array('class' => 'flex flex-wrap items-end gap-3 border-b border-line bg-canvas px-5 py-4')) ?>
				<div>
					<label class="label" for="nomor">Nomor</label>
					<input type="number" id="nomor" name="nomor" min="1" value="<?= (int) $nomorBerikut ?>" required class="input w-24">
				</div>
				<div>
					<label class="label" for="tanggal">Tanggal</label>
					<input type="date" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required class="input w-auto">
				</div>
				<button type="submit" class="btn-primary">Tambah Pertemuan</button>
			<?= form_close() ?>

			<?php if (! $pertemuan): ?>
				<div class="p-8 text-center text-sm text-muted">Belum ada pertemuan. Tambahkan pertemuan pertama di atas.</div>
			<?php else: ?>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-line">
						<thead class="bg-slate-50">
							<tr>
								<th class="th">Nomor</th>
								<th class="th">Tanggal</th>
								<th class="th">Sudah Diabsen</th>
								<th class="th text-right">Aksi</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-line">
							<?php foreach ($pertemuan as $pm): ?>
								<tr class="hover:bg-canvas">
									<td class="td">Ke-<?= (int) $pm->nomor ?></td>
									<td class="td whitespace-nowrap"><?= html_escape(tanggal_indo($pm->tanggal)) ?></td>
									<td class="td">
										<?= (int) $pm->terisi ?> peserta
										<?php if ($pm->terisi > 0): ?>
											<a href="<?= site_url('guru/absensi/riwayat/'.$pm->id) ?>" class="ml-1 text-sm font-semibold text-primary hover:underline">(lihat)</a>
										<?php endif; ?>
									</td>
									<td class="td text-right">
										<a href="<?= site_url('guru/absensi/isi/'.$pm->id) ?>" class="text-sm font-semibold text-primary hover:underline">
											<?= $pm->terisi > 0 ? 'Ubah Absensi' : 'Isi Absensi' ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</section>
	</div>

	<div class="space-y-5">
		<section class="card overflow-hidden">
			<div class="card-head">
				<h2 class="font-semibold text-ink">Peserta Aktif (<?= count($peserta) ?>)</h2>
			</div>
			<?php if (! $peserta): ?>
				<div class="p-8 text-center text-sm text-muted">Belum ada peserta aktif di kelas ini.</div>
			<?php else: ?>
				<ul class="divide-y divide-line text-sm">
					<?php foreach ($peserta as $ps): ?>
						<li class="px-5 py-3">
							<div class="font-medium text-ink"><?= html_escape($ps->nama) ?></div>
							<div class="text-xs text-muted">
								<?= html_escape($ps->no_wa) ?>
								&middot; Sejak <?= html_escape(tanggal_indo($ps->sejak)) ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
	</div>
</div>
