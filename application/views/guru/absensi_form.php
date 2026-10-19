<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Absensi &amp; Progres — Pertemuan <?= (int) $pertemuan->nomor ?></h1>
		<p class="mt-1 text-sm text-muted">
			<?= html_escape(label_kelas((array) $pertemuan)) ?> &middot;
			<?= html_escape(tanggal_indo($pertemuan->tanggal)) ?>
		</p>
	</div>
	<a href="<?= site_url('guru/kelas/detail/'.$pertemuan->kelas_id) ?>" class="btn-ghost btn-sm">&larr; Kembali ke Kelas</a>
</div>

<?php if (! $peserta): ?>
	<div class="card p-8 text-center text-sm text-muted">Tidak ada peserta aktif untuk diabsen pada kelas ini.</div>
<?php else: ?>
	<?= form_open('guru/absensi/simpan/'.$pertemuan->id) ?>
		<section class="card overflow-hidden">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-line">
					<thead class="bg-slate-50">
						<tr>
							<th class="th">Peserta</th>
							<th class="th">Kehadiran</th>
							<th class="th">Hal. Mulai</th>
							<th class="th">Hal. Selesai</th>
							<th class="th">Catatan Guru</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-line">
						<?php foreach ($peserta as $ps): ?>
							<?php
							$pid = (int) $ps->id;
							$row = isset($isian[$pid]) ? $isian[$pid] : NULL;
							$status = $row ? $row->status : 'hadir';
							$halMulai = $row && $row->hal_mulai !== NULL ? (int) $row->hal_mulai : '';
							$halSelesai = $row && $row->hal_selesai !== NULL ? (int) $row->hal_selesai : '';
							$catatan = $row ? (string) $row->catatan_guru : '';
							?>
							<tr class="hover:bg-canvas">
								<td class="td font-semibold text-ink"><?= html_escape($ps->nama) ?></td>
								<td class="td">
									<select name="status_<?= $pid ?>" class="input w-auto py-1.5">
										<?= pilih_opsi(array(
											'hadir' => 'Hadir',
											'izin' => 'Izin',
											'sakit' => 'Sakit',
											'alpa' => 'Alpa',
										), $status) ?>
									</select>
								</td>
								<td class="td">
									<input type="number" min="0" name="hal_mulai_<?= $pid ?>" value="<?= html_escape($halMulai) ?>" class="input w-24 py-1.5">
								</td>
								<td class="td">
									<input type="number" min="0" name="hal_selesai_<?= $pid ?>" value="<?= html_escape($halSelesai) ?>" class="input w-24 py-1.5">
								</td>
								<td class="td">
									<input type="text" name="catatan_<?= $pid ?>" value="<?= html_escape($catatan) ?>" maxlength="255" class="input min-w-48 py-1.5">
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<div class="mt-5 flex items-center gap-3">
			<button type="submit" class="btn-primary">Simpan Absensi</button>
			<a href="<?= site_url('guru/kelas/detail/'.$pertemuan->kelas_id) ?>" class="btn-ghost">Batal</a>
		</div>
	<?= form_close() ?>
<?php endif; ?>
