<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-5xl">
	<div class="mb-5 flex items-end justify-between">
		<h1 class="page-title">Pendaftaran Gelombang Saya</h1>
		<a href="<?= site_url('gelombang') ?>" class="btn-primary btn-sm">Lihat Gelombang Dibuka</a>
	</div>
	<div class="card overflow-x-auto">
		<table class="w-full text-sm">
			<thead>
				<tr>
					<th class="th">Program</th>
					<th class="th">Angkatan</th>
					<th class="th">Periode</th>
					<th class="th">Status</th>
					<th class="th">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($daftar as $d): ?>
				<tr class="border-t border-line">
					<td class="td"><?= html_escape($d->program_kode.' - '.$d->program_nama) ?></td>
					<td class="td"><?= html_escape($d->angkatan) ?></td>
					<td class="td text-muted"><?= tanggal_indo($d->tgl_buka) ?> s/d <?= tanggal_indo($d->tgl_tutup) ?></td>
					<td class="td"><?= badge($d->status, 'status') ?></td>
					<td class="td text-muted">
						<?php if ($d->status === 'diterima_ke_kelas'): ?>
							Diterima ke kelas. <?php if ($d->kelas_id_tujuan): ?>Silakan cek di menu Pendaftaran/Peserta.<?php endif; ?>
						<?php elseif ($d->status === 'dihubungi'): ?>
							Admin sedang menghubungi kamu.
						<?php elseif ($d->status === 'ditolak'): ?>
							Belum bisa bergabung pada gelombang ini.
						<?php else: ?>
							Menunggu proses admin.
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($daftar)): ?>
				<tr><td class="td text-muted" colspan="5">Belum ada pendaftaran minat gelombang.</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
