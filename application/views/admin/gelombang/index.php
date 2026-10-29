<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-6xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<h1 class="page-title">Gelombang Pendaftaran</h1>
		<a href="<?= site_url('admin/gelombang/tambah') ?>" class="btn-primary">Tambah Gelombang</a>
	</div>
	<div class="card overflow-x-auto">
		<table class="w-full text-sm">
			<thead>
				<tr>
					<th class="th">Program</th>
					<th class="th">Angkatan</th>
					<th class="th">Periode</th>
					<th class="th">Kuota</th>
					<th class="th">Status</th>
					<th class="th text-right">Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($gelombang as $g): ?>
				<tr class="border-t border-line">
					<td class="td"><?= html_escape($g->program_kode.' - '.$g->program_nama) ?></td>
					<td class="td"><?= html_escape($g->angkatan) ?></td>
					<td class="td text-muted"><?= tanggal_indo($g->tgl_buka) ?> s/d <?= tanggal_indo($g->tgl_tutup) ?></td>
					<td class="td"><?= $g->kuota ? (int) $g->kuota : '-' ?></td>
					<td class="td"><?= badge($g->status, 'status') ?></td>
					<td class="td text-right">
						<a href="<?= site_url('admin/gelombang/calon/'.$g->id) ?>" class="mr-2 text-sm font-semibold text-primary hover:underline">Calon</a>
						<a href="<?= site_url('admin/gelombang/edit/'.$g->id) ?>" class="mr-2 text-sm font-semibold text-ink hover:underline">Edit</a>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($gelombang)): ?>
				<tr><td class="td text-muted" colspan="6">Belum ada gelombang.</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
