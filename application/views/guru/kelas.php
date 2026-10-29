<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 class="page-title mb-5">Kelas Saya</h1>

<?php if (! $kelas): ?>
	<div class="card p-8 text-center text-sm text-muted">Belum ada kelas yang diampu.</div>
<?php else: ?>
	<div class="grid gap-4 sm:grid-cols-2">
		<?php foreach ($kelas as $k): ?>
			<div class="card p-5">
				<div class="mb-3 flex items-start justify-between gap-2">
					<h2 class="font-semibold text-ink"><?= html_escape(label_kelas((array) $k)) ?></h2>
					<?= badge('status', $k->status) ?>
				</div>
				<dl class="space-y-1 text-sm text-muted">
					<div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Program</dt>
						<dd><?= html_escape($k->program_nama) ?></dd></div>
					<div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Jadwal</dt>
						<dd><?= html_escape(jadwal_tampil($k)) ?></dd></div>
					<div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Peserta Aktif</dt>
						<dd><?= (int) $k->jumlah_peserta ?></dd></div>
				</dl>
				<a href="<?= site_url('guru/kelas/detail/'.$k->id) ?>" class="btn-primary btn-sm mt-4">Buka Kelas</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
