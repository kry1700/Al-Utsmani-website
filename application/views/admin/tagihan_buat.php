<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Buat Tagihan SPP</h1>
		<p class="mt-1 text-sm text-muted">Periode <?= html_escape(nama_bulan($periode)) ?> — nominal bisa diubah manual per peserta (prefill dari biaya SPP kelas).</p>
	</div>
	<a href="<?= site_url('admin/tagihan?periode='.$periode) ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Daftar Tagihan</a>
</div>

<form method="get" action="<?= site_url('admin/tagihan/buat') ?>" class="card mb-5 flex flex-wrap items-end gap-3 p-5">
	<div>
		<label class="label" for="periode">Periode</label>
		<input type="month" id="periode" name="periode" value="<?= html_escape($periode) ?>" class="input w-auto">
	</div>
	<button type="submit" class="btn-primary">Tampilkan</button>
</form>

<?php if (! $belum): ?>
	<div class="card p-8 text-center text-sm text-muted">
		Semua peserta aktif sudah ditagih untuk periode <?= html_escape(nama_bulan($periode)) ?>.
	</div>
<?php else: ?>
	<?= form_open('admin/tagihan/simpan') ?>
		<input type="hidden" name="periode" value="<?= html_escape($periode) ?>">

		<div class="card mb-5 flex flex-wrap items-end gap-4 p-5">
			<div>
				<label class="label" for="jatuh_tempo">Jatuh Tempo</label>
				<input type="date" id="jatuh_tempo" name="jatuh_tempo" value="<?= html_escape($jatuhTempo) ?>" required class="input w-auto">
			</div>
			<div class="text-sm text-muted"><?= count($belum) ?> peserta aktif belum ditagih pada periode ini.</div>
		</div>

		<section class="card overflow-hidden">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-line">
					<thead class="bg-slate-50">
						<tr>
							<th class="th w-16">
								<input type="checkbox" id="pilih-semua" class="h-4 w-4 accent-primary align-middle">
							</th>
							<th class="th">Peserta</th>
							<th class="th">No. WA</th>
							<th class="th">Kelas Aktif</th>
							<th class="th">Nominal (Rp)</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-line">
						<?php foreach ($belum as $baris): ?>
							<?php $pid = (int) $baris['peserta']->id; ?>
							<tr class="hover:bg-canvas">
								<td class="td">
									<input type="checkbox" name="pilih_<?= $pid ?>" value="1" checked class="pilih-baris h-4 w-4 accent-primary align-middle">
								</td>
								<td class="td font-semibold text-ink"><?= html_escape($baris['peserta']->nama) ?></td>
								<td class="td text-muted"><?= html_escape($baris['peserta']->no_wa) ?></td>
								<td class="td text-muted"><?= html_escape($baris['daftar_kelas']) ?></td>
								<td class="td">
									<input type="number" min="0" step="1000" name="nominal_<?= $pid ?>" value="<?= (int) $baris['nominal_hint'] ?>" class="input w-40">
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<div class="mt-5 flex items-center gap-3">
			<button type="submit" class="btn-primary">Buat Tagihan Terpilih</button>
			<a href="<?= site_url('admin/tagihan?periode='.$periode) ?>" class="btn-ghost">Batal</a>
		</div>
	<?= form_close() ?>

	<script>
	(function () {
		var semua = document.getElementById('pilih-semua');
		var baris = document.querySelectorAll('.pilih-baris');
		if (semua) {
			semua.addEventListener('change', function () {
				for (var i = 0; i < baris.length; i++) { baris[i].checked = semua.checked; }
			});
		}
	})();
	</script>
<?php endif; ?>
