<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Data Kelas</h1>
	<a href="<?= site_url('admin/kelas/tambah') ?>" class="btn-primary">Tambah Kelas</a>
</div>

<form method="get" action="<?= site_url('admin/kelas') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div>
		<label class="label" for="program">Program</label>
		<select id="program" name="program" class="input w-auto">
			<option value="">Semua</option>
			<?= pilih_opsi($programOptions, $filter['program_id']) ?>
		</select>
	</div>
	<div>
		<label class="label" for="level">Level</label>
		<select id="level" name="level" class="input w-auto">
			<option value="">Semua</option>
			<?= pilih_opsi($levelOptions, $filter['level_id']) ?>
		</select>
	</div>
	<div>
		<label class="label" for="cabang">Cabang</label>
		<select id="cabang" name="cabang" class="input w-auto">
			<option value="">Semua</option>
			<?php foreach ($cabangList as $c): ?>
				<option value="<?= (int) $c->id ?>" <?= (int) $filter['cabang_id'] === (int) $c->id ? 'selected' : '' ?>><?= html_escape($c->nama) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div>
		<label class="label" for="mode">Mode</label>
		<select id="mode" name="mode" class="input w-auto">
			<?= pilih_opsi(array('' => 'Semua', 'online' => 'Online', 'offline' => 'Offline'), $filter['mode']) ?>
		</select>
	</div>
	<div>
		<label class="label" for="status">Status</label>
		<select id="status" name="status" class="input w-auto">
			<?= pilih_opsi(array('' => 'Semua', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'), $filter['status']) ?>
		</select>
	</div>
	<button type="submit" class="btn-primary">Filter</button>
	<a href="<?= site_url('admin/kelas') ?>" class="btn-ghost">Reset</a>
</form>

<section class="card overflow-hidden">
	<?php if (! $kelas): ?>
		<div class="p-8 text-center text-sm text-muted">Tidak ada kelas yang cocok dengan filter.</div>
	<?php else: ?>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Kelas</th>
						<th class="th">Guru</th>
						<th class="th">Jadwal</th>
						<th class="th">Biaya Daftar</th>
						<th class="th">SPP / Bulan</th>
						<th class="th">Status</th>
						<th class="th text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php foreach ($kelas as $k): ?>
						<tr class="hover:bg-canvas">
							<td class="td">
								<div class="font-semibold text-ink"><?= html_escape(label_kelas((array) $k)) ?></div>
								<?php if ($k->program_kode === 'BILAL' && $k->tipe_peserta): ?>
									<div class="text-xs text-muted">Tipe: <?= html_escape(ucfirst($k->tipe_peserta)) ?><?= $k->nama_instansi ? ' — '.html_escape($k->nama_instansi) : '' ?></div>
								<?php endif; ?>
							</td>
							<td class="td text-muted"><?= html_escape($k->guru_nama) ?></td>
							<td class="td text-muted"><?= html_escape($k->jadwal) ?> <span class="text-slate-400">(<?= html_escape($k->zona_waktu) ?>)</span></td>
							<td class="td whitespace-nowrap"><?= rupiah($k->biaya_pendaftaran) ?></td>
							<td class="td whitespace-nowrap"><?= rupiah($k->biaya_spp) ?></td>
							<td class="td"><?= badge('status', $k->status) ?></td>
							<td class="td text-right whitespace-nowrap">
								<a href="<?= site_url('admin/kelas/edit/'.$k->id) ?>" class="mr-3 text-sm font-semibold text-primary hover:underline">Edit</a>
								<?= form_open('admin/kelas/setStatus/'.$k->id, array('class' => 'inline')) ?>
									<button type="submit" class="text-sm font-semibold <?= $k->status === 'aktif' ? 'text-rose-600 hover:underline' : 'text-primary hover:underline' ?>">
										<?= $k->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
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
