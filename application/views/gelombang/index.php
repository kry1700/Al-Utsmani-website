<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Pendaftaran</p>
			<h1 class="au-home-heading au-home-heading--h1">Pendaftaran Gelombang</h1>
			<p class="au-home-body au-home-body--lg">Informasi pendaftaran yang sedang dibuka berdasarkan tanggal. Daftarkan minat Anda sebelum periode berakhir.</p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('daftar') ?>" class="au-home-button au-home-button-primary">Daftar Kelas</a>
		</div>
	</header>

	<?php if (empty($gelombang)): ?>
		<div class="au-panel au-empty">
			<h2 class="au-empty__title">Belum ada gelombang yang dibuka</h2>
			<p>Silakan cek kembali nanti, atau langsung pilih kelas dari halaman daftar.</p>
			<p class="mt-4"><a href="<?= site_url('daftar') ?>" class="au-home-button au-home-button-secondary au-home-button--sm">Lihat kelas aktif</a></p>
		</div>
	<?php else: ?>
		<div class="au-card-grid">
			<?php foreach ($gelombang as $g): ?>
				<article class="au-kelas-card">
					<div class="au-kelas-card__top">
						<span class="au-chip au-chip--brand"><?= html_escape($g->program_kode) ?></span>
						<span class="au-home-caption"><?= html_escape($g->angkatan) ?></span>
					</div>
					<h2 class="au-kelas-card__title"><?= html_escape($g->program_nama) ?></h2>
					<div class="au-kelas-card__meta mt-2">
						<p>Pendaftaran: <strong><?= tanggal_indo($g->tgl_buka) ?></strong> s/d <strong><?= tanggal_indo($g->tgl_tutup) ?></strong></p>
					</div>
					<?php if ($g->keterangan): ?>
						<p class="au-home-body mt-3 flex-1"><?= nl2br(html_escape($g->keterangan)) ?></p>
					<?php endif; ?>
					<div class="au-kelas-card__actions">
						<?php if (! empty($user)): ?>
							<a href="<?= site_url('gelombang/daftar/'.$g->id) ?>" class="au-home-button au-home-button-primary au-home-button--sm">Daftar Minat</a>
						<?php else: ?>
							<a href="<?= site_url('auth/login') ?>" class="au-home-button au-home-button-primary au-home-button--sm">Login untuk Daftar Minat</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
