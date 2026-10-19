<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Data Peserta</h1>
	<a href="<?= site_url('admin/peserta/tambah') ?>" class="btn-primary">Tambah Peserta</a>
</div>

<form method="get" action="<?= site_url('admin/peserta') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div class="min-w-48 flex-1">
		<label class="label" for="q">Cari nama / no. WhatsApp</label>
		<input type="text" id="q" name="q" value="<?= html_escape($q) ?>" class="input">
	</div>
	<button type="submit" class="btn-primary">Cari</button>
	<?php if ($q !== ''): ?><a href="<?= site_url('admin/peserta') ?>" class="btn-ghost">Reset</a><?php endif; ?>
</form>

<section class="card overflow-hidden">
	<?php if (! $peserta): ?>
		<div class="p-8 text-center text-sm text-muted">Tidak ada data peserta<?= $q !== '' ? ' untuk pencarian "'.html_escape($q).'"' : '' ?>.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Nama</th>
						<th class="th">Akun</th>
						<th class="th">No. WA</th>
						<th class="th">Kemampuan</th>
						<th class="th">Kelas Aktif</th>
						<th class="th">Status Akun</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($peserta as $p): ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink"><?= html_escape($p->nama) ?></td>
							<td class="td text-muted">
								<?= html_escape($p->username) ?>
								<div class="text-xs text-slate-400"><?= html_escape($p->email) ?></div>
							</td>
							<td class="td text-muted"><?= html_escape($p->no_wa) ?></td>
							<td class="td text-muted"><?= $p->kemampuan_awal === 'hafalan' ? 'Hafalan' : 'Pemula' ?></td>
							<td class="td"><?= (int) $p->pendaftaran_aktif ?></td>
							<td class="td"><?= badge('status', $p->user_status) ?></td>
							<td class="td text-right">
								<a href="<?= site_url('admin/peserta/detail/'.$p->id) ?>" class="text-sm font-semibold text-primary hover:underline">Detail</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
