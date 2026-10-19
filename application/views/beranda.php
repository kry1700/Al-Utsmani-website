<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Beranda publik: hero besar full-bleed (teks kiri + gambar kanan), lalu statistik,
 * Metode Kami, pemilih cabang/program, carousel katalog program, dan cara mendaftar.
 *
 * Halaman ini memakai `main_penuh` (lihat Beranda::index()) sehingga <main> selebar
 * layar dan tiap section membungkus dirinya sendiri lewat `$wadah`.
 */
$hero = hero_gambar();
$wadah = 'mx-auto max-w-6xl px-4 sm:px-6';
?>
<!-- HERO (full-bleed) -->
<section class="relative overflow-hidden border-b border-line bg-gradient-to-br from-primary-soft via-canvas to-primary-soft/70">
	<div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl"></div>
	<div class="pointer-events-none absolute -bottom-32 -left-24 h-80 w-80 rounded-full bg-primary/10 blur-3xl"></div>

	<div class="<?= $wadah ?> relative grid items-center gap-10 py-12 md:min-h-[60vh] lg:min-h-[70vh] lg:grid-cols-2 lg:gap-14 lg:py-16">
		<div>
			<span class="mb-4 inline-block rounded-full border border-primary/25 bg-white/70 px-3 py-1 text-xs font-semibold text-primary">LBQ Al-Utsmani</span>
			<h1 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-ink sm:text-5xl lg:text-6xl">
				Yuk, Ngaji Bareng <span class="text-primary">Al-Utsmani</span>!
			</h1>
			<p class="max-w-xl text-lg font-medium leading-relaxed text-muted sm:text-xl">
				Belajar Quran, Mudah dan Menyenangkan
			</p>
			<div class="mt-7 flex flex-wrap gap-3">
				<a href="<?= site_url('katalog') ?>" class="btn-primary">Temukan Program</a>
				<a href="#metode" class="btn-outline">Pelajari Metode Kami</a>
			</div>
			<?php if (empty($user)): ?>
				<p class="mt-4 text-sm text-muted">
					Sudah punya akun?
					<a href="<?= site_url('auth/login') ?>" class="font-semibold text-primary underline underline-offset-4 hover:text-primary-dark">Login</a>
				</p>
			<?php endif; ?>
		</div>

		<div class="relative">
			<?php if ($hero): ?>
				<img src="<?= html_escape($hero) ?>" alt="Suasana belajar Al-Qur'an di LBQ Al-Utsmani"
					class="aspect-[4/3] w-full rounded-3xl object-cover shadow-float ring-1 ring-line">
			<?php else: ?>
				<div class="flex aspect-[4/3] w-full items-center justify-center rounded-3xl bg-gradient-to-br from-primary to-primary-deep shadow-float">
					<span class="text-6xl font-black tracking-wider text-white/90">AU</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- PINTASAN CEPAT -->
<div class="relative z-10 mx-auto -mt-8 max-w-4xl px-4 sm:-mt-10 sm:px-6">
	<div class="grid grid-cols-2 gap-2 rounded-2xl border border-line bg-white p-3 shadow-float sm:grid-cols-4 sm:gap-3 sm:p-5">
		<a href="<?= site_url('katalog') ?>" class="group flex flex-col items-center gap-2 rounded-xl px-2 py-3 text-center transition-colors hover:bg-primary-soft">
			<span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary transition-colors group-hover:bg-primary group-hover:text-white">
				<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
			</span>
			<span class="text-xs font-semibold text-ink sm:text-sm">Program</span>
		</a>

		<a href="<?= site_url('daftar') ?>" class="group flex flex-col items-center gap-2 rounded-xl px-2 py-3 text-center transition-colors hover:bg-primary-soft">
			<span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary transition-colors group-hover:bg-primary group-hover:text-white">
				<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
			</span>
			<span class="text-xs font-semibold text-ink sm:text-sm">Daftar Kelas</span>
		</a>

		<a href="#daftar-kelas" class="group flex flex-col items-center gap-2 rounded-xl px-2 py-3 text-center transition-colors hover:bg-primary-soft">
			<span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary transition-colors group-hover:bg-primary group-hover:text-white">
				<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
			</span>
			<span class="text-xs font-semibold text-ink sm:text-sm">Cabang</span>
		</a>

		<a href="#cara-mendaftar" class="group flex flex-col items-center gap-2 rounded-xl px-2 py-3 text-center transition-colors hover:bg-primary-soft">
			<span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary transition-colors group-hover:bg-primary group-hover:text-white">
				<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
			</span>
			<span class="text-xs font-semibold text-ink sm:text-sm">Cara Mendaftar</span>
		</a>
	</div>
</div>

<div class="<?= $wadah ?> space-y-10 py-10">

	<!-- STATISTIK -->
	<section class="grid grid-cols-3 gap-4">
		<div class="stat-card text-center">
			<div class="text-2xl font-bold text-primary sm:text-3xl"><?= count($programs) ?></div>
			<div class="text-sm text-muted">Program</div>
		</div>
		<div class="stat-card text-center">
			<div class="text-2xl font-bold text-primary sm:text-3xl"><?= (int) $jumlah_kelas ?></div>
			<div class="text-sm text-muted">Kelas Aktif</div>
		</div>
		<div class="stat-card text-center">
			<div class="text-2xl font-bold text-primary sm:text-3xl"><?= (int) $jumlah_cabang ?></div>
			<div class="text-sm text-muted">Cabang Aktif</div>
		</div>
	</section>

	<!-- METODE KAMI -->
	<section id="metode" class="scroll-mt-24">
		<h2 class="text-2xl font-bold text-ink sm:text-3xl">Metode Kami</h2>
		<p class="mb-5 text-sm text-muted sm:text-base">Pendekatan pembelajaran Al-Utsmani yang telah terbukti sejak 1990.</p>
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
	<section id="daftar-kelas" class="scroll-mt-24">
		<h2 class="mb-1 text-2xl font-bold text-ink sm:text-3xl">Daftar Kelas</h2>
		<p class="mb-5 text-sm text-muted sm:text-base">Pilih mulai dari cabang atau dari program — keduanya menuju daftar kelas yang bisa Anda ikuti.</p>
		<?php $this->load->view('daftar/_pilih', array(
			'programs' => $programs,
			'cabangs' => $cabangs,
			'rekap' => $rekap,
			'aksi' => site_url('daftar/kelas'),
		)); ?>
	</section>

	<!-- KATALOG PROGRAM (CAROUSEL) -->
	<section>
		<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
			<div>
				<h2 class="text-2xl font-bold text-ink sm:text-3xl">Program Kami</h2>
				<p class="mt-1 text-sm text-muted sm:text-base">Empat program untuk kebutuhan usia dan lembaga yang berbeda.</p>
			</div>
			<a href="<?= site_url('katalog') ?>" class="text-sm font-semibold text-primary hover:underline">Buka katalog &rarr;</a>
		</div>
		<?php $this->load->view('katalog/carousel', array('programs' => $programs, 'rekap' => $rekap)); ?>
	</section>

	<!-- CARA MENDAFTAR -->
	<section id="cara-mendaftar" class="card scroll-mt-24 p-6 text-sm leading-relaxed text-muted sm:text-base">
		<h2 class="mb-3 text-lg font-bold text-ink">Cara Mendaftar</h2>
		<ol class="ml-5 list-decimal space-y-1">
			<li>Pilih <strong class="text-ink">cabang</strong> dan <strong class="text-ink">program</strong> (atau sebaliknya), lalu pilih kelas yang sesuai jadwal Anda.</li>
			<li>Isi data registrasi (akun + data peserta) dan unggah bukti pembayaran bila ada biaya pendaftaran.</li>
			<li>Tunggu verifikasi admin — pendaftaran aktif setelah disetujui.</li>
			<li>Level/rank Anda ditetapkan pengelola melalui proses <strong class="text-ink">penempatan</strong>, lalu tampil di profil singkat Anda.</li>
			<li>Ikuti kelas, absensi, dan progres belajar dari dashboard Anda.</li>
		</ol>
	</section>

</div>
