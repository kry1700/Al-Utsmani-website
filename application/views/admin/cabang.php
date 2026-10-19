<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Data Cabang</h1>
	<a href="<?= site_url('admin/cabang/tambah') ?>" class="btn-primary">Tambah Cabang</a>
</div>

<?php if (! $perWilayah): ?>
	<div class="card p-8 text-center text-sm text-muted">Belum ada data cabang.</div>
<?php else: ?>
	<div class="space-y-5">
		<?php foreach ($perWilayah as $wilayah => $daftar): ?>
			<section class="card overflow-hidden">
				<div class="card-head">
					<h2 class="font-semibold text-ink">Wilayah <?= html_escape($wilayah) ?></h2>
					<span class="badge bg-primary-soft text-primary"><?= count($daftar) ?> cabang</span>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-line">
						<thead class="bg-slate-50">
							<tr>
								<th class="th">Nama Cabang</th>
								<th class="th">Status</th>
								<th class="th text-right">Aksi</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-line">
							<?php foreach ($daftar as $c): ?>
								<tr class="hover:bg-canvas">
									<td class="td font-semibold text-ink"><?= html_escape($c->nama) ?></td>
									<td class="td"><?= badge('status', $c->status) ?></td>
									<td class="td text-right whitespace-nowrap">
										<a href="<?= site_url('admin/cabang/edit/'.$c->id) ?>" class="mr-3 text-sm font-semibold text-primary hover:underline">Edit</a>
										<?= form_open('admin/cabang/setStatus/'.$c->id, array('class' => 'inline')) ?>
											<button type="submit" class="text-sm font-semibold <?= $c->status === 'aktif' ? 'text-rose-600 hover:underline' : 'text-primary hover:underline' ?>">
												<?= $c->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
											</button>
										<?= form_close() ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
