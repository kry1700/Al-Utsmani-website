<?php defined('BASEPATH') OR exit('No direct script access allowed');
$hero = hero_gambar();
?>
<!-- HERO -->
<section class="relative mb-8 overflow-hidden rounded-2xl text-white <?= $hero ? '' : 'bg-gradient-to-br from-emerald-700 to-teal-800' ?>">
	<?php if ($hero): ?>
		<img src="<?= html_escape($hero) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
		<div class="absolute inset-0 bg-emerald-900/75"></div>
	<?php endif; ?>
	<div class="relative p-6 sm:p-10">
		<span class="mb-4 inline-block rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-medium">LBQ Al-Utsmani</span>
		<h1 class="mb-3 text-2xl font-bold leading-tight sm:text-4xl">Yuk, Ngaji Bareng Al-Utsmani!</h1>
		<p class="max-w-3xl leading-relaxed text-emerald-50">
			Belajar membaca Al-Qur'an dengan metode Al-Utsmani yang telah digunakan sejak 1990. Pembelajaran dirancang
			secara bertahap, terarah, dan menyenangkan, dengan dukungan kurikulum dan silabus terstruktur, kategori level
			pembelajaran, evaluasi berkala, serta guru tersertifikasi.
		</p>
		<div class="mt-6 flex flex-wrap gap-3">
			<a href="<?= site_url('katalog') ?>" class="rounded-full bg-white px-5 py-2.5 font-semibold text-emerald-800 transition-colors hover:bg-emerald-50">Temukan Program</a>
			<a href="#metode" class="rounded-full border border-white/70 px-5 py-2.5 font-medium text-white transition-colors hover:bg-white/10">Pelajari Metode Kami</a>
			<?php if (empty($user)): ?>
				<a href="<?= site_url('auth/login') ?>" class="px-2 py-2.5 text-emerald-50 underline underline-offset-4 hover:text-white">Sudah punya akun? Login</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- STATISTIK -->
<section class="mb-8 grid grid-cols-3 gap-4">
	<div class="stat-card text-center">
		<div class="text-2xl font-bold text-primary"><?= count($programs) ?></div>
		<div class="text-sm text-muted">Program</div>
	</div>
	<div class="stat-card text-center">
		<div class="text-2xl font-bold text-primary"><?= (int) $jumlah_kelas ?></div>
		<div class="text-sm text-muted">Kelas Aktif</div>
	</div>
	<div class="stat-card text-center">
		<div class="text-2xl font-bold text-primary"><?= (int) $jumlah_cabang ?></div>
		<div class="text-sm text-muted">Cabang Aktif</div>
	</div>
</section>

<!-- METODE KAMI -->
<section id="metode" class="mb-10 scroll-mt-24">
	<h2 class="text-xl font-bold text-ink">Metode Kami</h2>
	<p class="mb-4 text-sm text-muted">Pendekatan pembelajaran Al-Utsmani yang telah terbukti sejak 1990.</p>
	<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<?php
		$metode = array(
			array('Metode Al-Utsmani', 'Berpengalaman sejak 1990.'),
			array('Pembelajaran Bertahap', 'Sesuai level dan kemampuan peserta.'),
			array('Kurikulum Terstruktur', 'Dilengkapi kurikulum dan silabus yang jelas.'),
			array('Evaluasi Berkala', 'Memantau perkembangan belajar peserta.'),
			array('Guru Tersertifikasi', 'Dibimbing oleh guru yang telah memenuhi standar sertifikasi.'),
		);
		foreach ($metode as $m): ?>
			<div class="card p-5">
				<div class="mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-primary-soft text-primary">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
				</div>
				<h3 class="mb-1 text-sm font-semibold text-ink"><?= html_escape($m[0]) ?></h3>
				<p class="text-sm leading-relaxed text-muted"><?= html_escape($m[1]) ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<!-- PEMILIH CABANG / PROGRAM -->
<section class="mb-10">
	<h2 class="mb-1 text-xl font-bold text-ink">Daftar Kelas</h2>
	<p class="mb-4 text-sm text-muted">Pilih mulai dari cabang atau dari program — keduanya menuju daftar kelas yang bisa Anda ikuti.</p>
	<?php $this->load->view('daftar/_pilih', array(
		'programs' => $programs,
		'cabangs' => $cabangs,
		'rekap' => $rekap,
		'aksi' => site_url('daftar/kelas'),
	)); ?>
</section>

<!-- KATALOG PROGRAM (CAROUSEL) -->
<section class="mb-10">
	<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
		<div>
			<h2 class="text-xl font-bold text-ink">Program Kami</h2>
			<p class="text-sm text-muted">Empat program untuk kebutuhan usia dan lembaga yang berbeda.</p>
		</div>
		<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-primary hover:underline">Buka katalog &rarr;</a>
	</div>
	<?php $this->load->view('katalog/carousel', array('programs' => $programs, 'rekap' => $rekap)); ?>
</section>

<!-- CARA MENDAFTAR -->
<section class="card p-5 text-sm leading-relaxed text-muted">
	<h2 class="mb-2 text-base font-semibold text-ink">Cara Mendaftar</h2>
	<ol class="ml-5 list-decimal space-y-1">
		<li>Pilih <strong class="text-ink">cabang</strong> dan <strong class="text-ink">program</strong> (atau sebaliknya), lalu pilih kelas yang sesuai jadwal Anda.</li>
		<li>Isi data registrasi (akun + data peserta) dan unggah bukti pembayaran bila ada biaya pendaftaran.</li>
		<li>Tunggu verifikasi admin — pendaftaran aktif setelah disetujui.</li>
		<li>Level/rank Anda ditetapkan pengelola melalui proses <strong class="text-ink">penempatan</strong>, lalu tampil di profil singkat Anda.</li>
		<li>Ikuti kelas, absensi, dan progres belajar dari dashboard Anda.</li>
	</ol>
</section>
