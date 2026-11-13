<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Katalog</p>
			<h1 class="au-home-heading au-home-heading--h1">Program &amp; Kelas</h1>
			<p class="au-home-body au-home-body--lg"><?= (int) $jumlah_kelas ?> kelas aktif di <?= count($cabangs) ?> cabang. Pilih program untuk melihat kelas dan jadwalnya.</p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('daftar') ?>" class="au-home-button au-home-button-primary">Mulai Daftar</a>
			<a href="<?= site_url('gelombang') ?>" class="au-home-button au-home-button-secondary">Info Gelombang</a>
		</div>
	</header>

	<section class="mb-10" aria-labelledby="judul-program">
		<h2 id="judul-program" class="au-home-heading au-home-heading--h3 mb-4">Program unggulan</h2>
		<?php $this->load->view('katalog/carousel', array('programs' => $programs, 'rekap' => $rekap)); ?>
	</section>

	<section class="mb-10" aria-labelledby="judul-semua-program">
		<h2 id="judul-semua-program" class="au-home-heading au-home-heading--h3 mb-4">Semua Program</h2>
		<div class="au-card-grid">
			<?php foreach ($programs as $p): $pid = (int) $p->id; $info = isset($rekap[$pid]) ? $rekap[$pid] : array('jumlah' => 0, 'cabang' => array()); ?>
				<article class="au-kelas-card">
					<div class="au-kelas-card__top">
						<h3 class="au-kelas-card__title"><span class="text-primary"><?= html_escape($p->kode) ?></span> &mdash; <?= html_escape($p->nama) ?></h3>
						<span class="au-chip au-chip--brand"><?= (int) $info['jumlah'] ?> kelas</span>
					</div>
					<p class="au-home-body flex-1"><?= html_escape($p->deskripsi) ?></p>
					<div class="au-kelas-card__actions mt-4">
						<a href="<?= site_url('katalog/program/'.$pid) ?>" class="au-home-button au-home-button-secondary au-home-button--sm">Lihat Kelas</a>
						<a href="<?= site_url('daftar/kelas?program='.$pid) ?>" class="au-home-button au-home-button-primary au-home-button--sm">Daftar</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="au-notice" role="note">
		<strong>Catatan level:</strong> level/rank bukan pilihan peserta. Level ditetapkan pengelola melalui proses penempatan setelah Anda mendaftar, lalu tampil pada profil singkat Anda.
	</section>
</div>
