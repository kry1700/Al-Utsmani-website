<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<div>
		<h1 class="page-title">Katalog Program</h1>
		<p class="mt-1 text-sm text-muted"><?= (int) $jumlah_kelas ?> kelas aktif di <?= count($cabangs) ?> cabang. Pilih program untuk melihat kelasnya.</p>
	</div>
	<a href="<?= site_url('daftar') ?>" class="btn-primary">Mulai Daftar</a>
</div>

<section class="mb-8">
	<?php $this->load->view('katalog/carousel', array('programs' => $programs, 'rekap' => $rekap)); ?>
</section>

<section class="mb-8">
	<h2 class="mb-3 text-lg font-bold text-ink">Semua Program</h2>
	<div class="grid gap-4 sm:grid-cols-2">
		<?php foreach ($programs as $p): $pid = (int) $p->id; $info = isset($rekap[$pid]) ? $rekap[$pid] : array('jumlah' => 0, 'cabang' => array()); ?>
			<div class="card flex flex-col p-5">
				<div class="mb-1 flex items-center justify-between gap-2">
					<h3 class="font-semibold text-ink"><span class="text-primary"><?= html_escape($p->kode) ?></span> — <?= html_escape($p->nama) ?></h3>
					<span class="badge bg-primary-soft text-primary whitespace-nowrap"><?= (int) $info['jumlah'] ?> kelas</span>
				</div>
				<p class="flex-1 text-sm text-muted"><?= html_escape($p->deskripsi) ?></p>
				<div class="mt-4 flex gap-2">
					<a href="<?= site_url('katalog/program/'.$pid) ?>" class="btn-outline btn-sm">Lihat Kelas</a>
					<a href="<?= site_url('daftar/kelas?program='.$pid) ?>" class="btn-primary btn-sm">Daftar</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="rounded-2xl border border-line bg-primary-soft p-5 text-sm leading-relaxed text-primary-dark">
	<strong>Catatan level:</strong> level/rank bukan pilihan peserta. Level ditetapkan pengelola melalui proses penempatan setelah Anda mendaftar, lalu tampil pada profil singkat Anda.
</section>
