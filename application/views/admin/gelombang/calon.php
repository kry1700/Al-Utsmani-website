<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-6xl">
	<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
		<div>
			<h1 class="page-title">Calon Pendaftar</h1>
			<p class="text-sm text-muted"><?= html_escape($gelombang->program_kode.' - '.$gelombang->program_nama) ?> | <?= html_escape($gelombang->angkatan) ?> (<?= tanggal_indo($gelombang->tgl_buka) ?> s/d <?= tanggal_indo($gelombang->tgl_tutup) ?>)</p>
		</div>
		<a href="<?= site_url('admin/gelombang') ?>" class="btn-ghost btn-sm">&larr; Kembali</a>
	</div>
	<div class="mb-4 flex flex-wrap gap-2">
		<a href="<?= site_url('admin/gelombang/calon/'.$gelombang->id) ?>" class="btn-ghost btn-sm">Semua</a>
		<a href="<?= site_url('admin/gelombang/calon/'.$gelombang->id.'?status=menunggu') ?>" class="btn-ghost btn-sm">Menunggu</a>
		<a href="<?= site_url('admin/gelombang/calon/'.$gelombang->id.'?status=dihubungi') ?>" class="btn-ghost btn-sm">Dihubungi</a>
		<a href="<?= site_url('admin/gelombang/calon/'.$gelombang->id.'?status=diterima_ke_kelas') ?>" class="btn-ghost btn-sm">Diterima</a>
		<a href="<?= site_url('admin/gelombang/calon/'.$gelombang->id.'?status=ditolak') ?>" class="btn-ghost btn-sm">Ditolak</a>
	</div>
	<div class="card overflow-x-auto">
		<table class="w-full text-sm">
			<thead>
				<tr>
					<th class="th">Nama</th>
					<th class="th">Kontak</th>
					<th class="th">Peserta</th>
					<th class="th">Tgl Daftar</th>
					<th class="th">Status</th>
					<th class="th text-right">Proses</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($calon as $c): ?>
				<tr class="border-t border-line align-top">
					<td class="td">
						<div class="font-medium"><?= html_escape($c->nama_calon) ?></div>
						<?php if ($c->catatan_admin): ?><div class="text-xs text-muted">Catatan: <?= html_escape($c->catatan_admin) ?></div><?php endif; ?>
					</td>
					<td class="td text-muted">
						<div><?= html_escape($c->no_hp) ?></div>
						<div><?= html_escape($c->email) ?></div>
					</td>
					<td class="td"><?= $c->peserta_nama ? html_escape($c->peserta_nama) : '<span class="text-muted">Belum ada profil</span>' ?></td>
					<td class="td text-muted"><?= tanggal_indo($c->created_at) ?></td>
					<td class="td"><?= badge($c->status, 'status') ?></td>
					<td class="td text-right">
						<?php if ($c->status === 'menunggu' || $c->status === 'dihubungi'): ?>
							<?= form_open('admin/gelombang/proses/'.$c->id, array('class'=>'mb-2')) ?>
								<input type="hidden" name="aksi" value="dihubungi">
								<input type="text" name="catatan_admin" placeholder="Catatan" class="input mb-1" style="max-width:200px">
								<button class="btn-ghost btn-sm">Tandai Dihubungi</button>
							<?= form_close() ?>
							<?= form_open('admin/gelombang/proses/'.$c->id, array('class'=>'mb-2')) ?>
								<input type="hidden" name="aksi" value="tolak">
								<input type="text" name="catatan_admin" placeholder="Catatan" class="input mb-1" style="max-width:200px">
								<button class="btn-ghost btn-sm text-rose-600">Tolak</button>
							<?= form_close() ?>
							<?= form_open('admin/gelombang/proses/'.$c->id) ?>
								<input type="hidden" name="aksi" value="terima">
								<select name="kelas_id" required class="input mb-1" style="max-width:220px">
									<option value="">— Pilih Kelas Tujuan —</option>
									<?php foreach ($kelasOptions as $k): ?>
										<option value="<?= (int) $k->id ?>"><?= html_escape($k->program_kode.' | '.$k->cabang_nama.' | '.$k->level_nama.' | '.($k->jadwal ? $k->jadwal : '').' '.($k->zona_waktu ? '('.$k->zona_waktu.')' : '')) ?></option>
									<?php endforeach; ?>
								</select>
								<div class="text-xs text-muted mb-1">Penempatan level hanya opsi jika peserta belum punya level (bisa ditindaklanjuti di menu Penempatan).</div>
								<input type="text" name="catatan_admin" placeholder="Catatan" class="input mb-1" style="max-width:220px">
								<button class="btn-primary btn-sm">Terima &amp; Masukkan ke Kelas</button>
							<?= form_close() ?>
						<?php else: ?>
							<span class="text-xs text-muted"><?= $c->kelas_id ? 'Sudah diproses' : '-' ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($calon)): ?>
				<tr><td class="td text-muted" colspan="6">Belum ada calon pendaftar.</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
