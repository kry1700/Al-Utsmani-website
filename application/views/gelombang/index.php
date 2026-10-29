<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mx-auto max-w-6xl px-4 py-8">
	<div class="mb-6">
		<h1 class="text-2xl font-bold text-ink">Pendaftaran Gelombang</h1>
		<p class="text-muted">Informasi pendaftaran yang sedang dibuka berdasarkan tanggal.</p>
	</div>
	<div class="grid gap-4 md:grid-cols-2">
		<?php foreach ($gelombang as $g): ?>
		<div class="card">
			<div class="card-body">
				<div class="mb-2 flex items-center justify-between">
					<span class="badge bg-primary/10 text-primary"><?= html_escape($g->program_kode) ?></span>
					<span class="text-sm text-muted"><?= html_escape($g->angkatan) ?></span>
				</div>
				<h2 class="text-lg font-semibold text-ink mb-1"><?= html_escape($g->program_nama) ?></h2>
				<p class="text-sm text-muted mb-3">Pendaftaran: <?= tanggal_indo($g->tgl_buka) ?> s/d <?= tanggal_indo($g->tgl_tutup) ?></p>
				<?php if ($g->keterangan): ?><p class="text-sm text-ink mb-4"><?= nl2br(html_escape($g->keterangan)) ?></p><?php endif; ?>
				<?php if ($this->session->userdata('logged_in')): ?>
					<a href="<?= site_url('gelombang/daftar/'.$g->id) ?>" class="btn-primary">Daftar Minat</a>
				<?php else: ?>
					<a href="<?= site_url('auth/login') ?>" class="btn-primary">Login untuk Daftar Minat</a>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
		<?php if (empty($gelombang)): ?>
		<div class="card md:col-span-2"><div class="card-body text-center text-muted">Belum ada gelombang yang sedang dibuka.</div></div>
		<?php endif; ?>
	</div>
</div>
