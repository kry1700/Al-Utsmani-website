<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Data Guru</h1>
	<a href="<?= site_url('admin/guru/tambah') ?>" class="btn-primary">Tambah Guru</a>
</div>

<section class="card overflow-hidden">
	<?php if (! $guru): ?>
		<div class="p-8 text-center text-sm text-muted">Belum ada data guru.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Nama</th>
						<th class="th">Akun</th>
						<th class="th">No. WA</th>
						<th class="th">Kelas Aktif</th>
						<th class="th">Status</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($guru as $g): ?>
						<tr class="hover:bg-canvas">
							<td class="td font-semibold text-ink"><?= html_escape($g->nama) ?></td>
							<td class="td text-muted">
								<?= html_escape($g->username) ?>
								<div class="text-xs text-slate-400"><?= html_escape($g->email) ?></div>
							</td>
							<td class="td text-muted"><?= html_escape($g->no_wa) ?></td>
							<td class="td"><?= (int) $g->jumlah_kelas_aktif ?></td>
							<td class="td"><?= badge('status', $g->status) ?></td>
							<td class="td text-right whitespace-nowrap">
								<a href="<?= site_url('admin/guru/edit/'.$g->id) ?>" class="mr-3 text-sm font-semibold text-primary hover:underline">Edit</a>
								<?= form_open('admin/guru/setStatus/'.$g->id, array('class' => 'inline')) ?>
									<button type="submit" class="text-sm font-semibold <?= $g->status === 'aktif' ? 'text-rose-600 hover:underline' : 'text-primary hover:underline' ?>">
										<?= $g->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
									</button>
								<?= form_close() ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
