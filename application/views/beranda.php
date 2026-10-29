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

// Program yang sedang berjalan: 1 baris per program (bukan per level), diambil
// dari agregat `Kelas_model::rekapProgram()` (jumlah kelas + daftar cabang).
$programBerjalan = array();
if (isset($rekap) && is_array($rekap) && isset($programs) && is_array($programs)) {
	foreach ($programs as $p) {
		$pid = (int) $p->id;
		if (empty($rekap[$pid]['kelas'])) {
			continue;
		}
		$cabangProgram = array();
		if (! empty($rekap[$pid]['cabang'])) {
			foreach ($rekap[$pid]['cabang'] as $c) {
				$cabangProgram[] = $c['nama'];
			}
		}
		$programBerjalan[] = array(
			'kode' => $p->kode,
			'nama' => $p->nama,
			'jumlah' => (int) $rekap[$pid]['jumlah'],
			'cabang' => $cabangProgram,
		);
	}
	$programBerjalan = array_slice($programBerjalan, 0, 3);
}
?>
<!-- HERO (full-screen background slider) -->
<?php
$slideList = (isset($gambarHeroSlider) && is_array($gambarHeroSlider)) ? $gambarHeroSlider : array();
if (empty($slideList) && $hero) {
	$slideList[] = array('img' => $hero, 'kode' => 'HERO', 'nama' => 'Kelas Al-Utsmani', 'status' => 'berjalan', 'angkatan' => '', 'periode' => '');
}
?>
<section class="relative isolate border-b border-line bg-primary-deep">
	<!-- Lapisan gambar: ditarik ke atas agar menimpa area navbar (tanpa celah) -->
	<div id="hero-slider" class="absolute inset-x-0 -top-40 bottom-0" data-interval="5000">
		<?php if (!empty($slideList)): ?>
			<?php foreach ($slideList as $i => $sl): ?>
				<div class="hero-slide absolute inset-0 transition-opacity duration-1000 <?= $i === 0 ? 'opacity-100' : 'pointer-events-none opacity-0' ?>" data-slide="<?= (int) $i ?>">
					<img src="<?= html_escape($sl['img']) ?>" alt="Kelas <?= html_escape($sl['nama']) ?> di LBQ Al-Utsmani"
						class="h-full w-full object-cover">
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="absolute inset-0 bg-gradient-to-br from-primary to-primary-deep"></div>
		<?php endif; ?>

		<!-- Overlay agar teks terbaca -->
		<div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/15"></div>
		<div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/70 to-transparent"></div>
	</div>

	<div class="<?= $wadah ?> relative flex min-h-[85vh] flex-col justify-center py-16 lg:min-h-[calc(100vh-72px)] lg:py-20">
		<div class="grid items-center gap-10 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-14">
			<div class="max-w-2xl text-white">
				<span class="mb-4 inline-block rounded-full border border-white/30 bg-white/10 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">LBQ Al-Utsmani</span>
				<h1 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl xl:text-7xl">
					Yuk, Ngaji <span class="text-primary-light">Bareng Al-Utsmani</span>
				</h1>
				<p class="max-w-xl text-lg font-medium leading-relaxed text-white/85 sm:text-xl">
					Belajar Al-Qur'an dengan metode terstruktur, bertahap, dan menyenangkan untuk semua kalangan.
				</p>
				<div class="mt-8 flex flex-wrap gap-3">
					<a href="<?= site_url('katalog') ?>" class="btn-primary px-6 py-2.5">Temukan Program</a>
					<a href="<?= site_url('daftar') ?>" class="rounded-full border border-white/50 bg-white/10 px-6 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/20">Daftar Sekarang</a>
				</div>
				<?php if (empty($user)): ?>
					<p class="mt-4 text-sm text-white/70">
						Sudah punya akun?
						<a href="<?= site_url('auth/login') ?>" class="font-semibold text-white underline underline-offset-4 hover:text-primary-light">Login</a>
					</p>
				<?php endif; ?>
			</div>

			<!-- Card info: glass, hanya desktop (di mobile tampil di bawah hero) -->
			<div class="hidden space-y-3 lg:block">
				<div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-md">
					<div class="mb-2 flex items-center justify-between">
						<h2 class="text-sm font-semibold">Program yang Berjalan</h2>
						<a href="<?= site_url('katalog') ?>" class="text-xs font-semibold text-primary-light hover:underline">Lihat semua</a>
					</div>
					<div class="space-y-2 text-sm">
						<?php if (!empty($programBerjalan)): ?>
							<?php foreach ($programBerjalan as $pb): ?>
								<div class="rounded-xl bg-black/25 px-3 py-2">
									<div class="flex items-baseline justify-between gap-2">
										<span class="font-semibold"><?= html_escape($pb['kode']) ?></span>
										<span class="whitespace-nowrap text-xs text-white/60"><?= (int) $pb['jumlah'] ?> kelas</span>
									</div>
									<div class="text-xs text-white/80"><?= html_escape($pb['nama']) ?></div>
									<?php if (!empty($pb['cabang'])): ?>
										<div class="text-xs text-white/60"><?= html_escape(implode(' · ', $pb['cabang'])) ?></div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<p class="text-xs text-white/70">Belum ada program berjalan.</p>
						<?php endif; ?>
					</div>
				</div>

				<div class="rounded-2xl border border-primary-light/40 bg-white/10 p-4 text-white backdrop-blur-md">
					<div class="mb-2 flex items-center justify-between">
						<h2 class="text-sm font-semibold">Coming Soon</h2>
						<a href="<?= site_url('gelombang') ?>" class="text-xs font-semibold text-primary-light hover:underline">Lihat semua</a>
					</div>
					<div class="space-y-2 text-sm">
						<?php if (!empty($gelombang)): ?>
							<?php foreach ($gelombang as $g): ?>
								<div class="rounded-xl border border-white/15 bg-black/25 px-3 py-2">
									<div class="flex items-start justify-between gap-2">
										<div>
											<div class="font-medium"><?= html_escape($g->program_kode) ?> · <?= html_escape($g->angkatan) ?></div>
											<div class="text-xs text-white/70"><?= tanggal_indo($g->tgl_buka) ?> s/d <?= tanggal_indo($g->tgl_tutup) ?></div>
										</div>
										<span class="whitespace-nowrap rounded-full bg-primary-light/25 px-2 py-0.5 text-xs text-white">Segera</span>
									</div>
									<?php if ($this->session->userdata('logged_in')): ?>
										<a href="<?= site_url('gelombang/daftar/'.$g->id) ?>" class="mt-2 inline-block text-xs font-semibold text-primary-light hover:underline">Daftar Minat</a>
									<?php else: ?>
										<a href="<?= site_url('auth/login') ?>" class="mt-2 inline-block text-xs font-semibold text-primary-light hover:underline">Login untuk daftar minat</a>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<p class="text-xs text-white/70">Tidak ada gelombang yang sedang dibuka.</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<?php if (!empty($slideList)): ?>
			<!-- Label slide (di dalam gambar) + dot indikator -->
			<div class="mt-10 flex flex-wrap items-end justify-between gap-4">
				<div class="relative min-h-[3.5rem] flex-1">
					<?php foreach ($slideList as $i => $sl): ?>
						<?php $berjalan = (isset($sl['status']) && $sl['status'] === 'berjalan'); ?>
						<div class="hero-label <?= $i === 0 ? '' : 'hidden' ?>" data-label="<?= (int) $i ?>">
							<span class="mb-1.5 inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide <?= $berjalan ? 'bg-primary text-white' : 'bg-primary-light text-primary-deep' ?>">
								<?= $berjalan ? 'Kelas Berjalan' : 'Segera Hadir' ?>
							</span>
							<div class="text-sm font-bold text-white sm:text-base"><?= html_escape($sl['kode']) ?> — <?= html_escape($sl['nama']) ?></div>
							<div class="text-xs text-white/70">
								<?php if ($berjalan): ?>
									Kelas sedang berlangsung
								<?php else: ?>
									<?= html_escape($sl['angkatan']) ?><?= !empty($sl['periode']) ? ' • '.$sl['periode'] : '' ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if (count($slideList) > 1): ?>
					<div class="flex items-center gap-2 pb-1">
						<?php foreach ($slideList as $i => $sl): ?>
							<span class="hero-dot h-2 rounded-full bg-white transition-all duration-300 <?= $i === 0 ? 'w-6 opacity-100' : 'w-2 opacity-40' ?>" data-dot="<?= (int) $i ?>"></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if (count($slideList) > 1): ?>
				<script>
				(function () {
					var root = document.getElementById('hero-slider');
					if (! root) { return; }
					var slides = root.querySelectorAll('.hero-slide');
					var labels = document.querySelectorAll('.hero-label');
					var dots = document.querySelectorAll('.hero-dot');
					if (slides.length < 2) { return; }
					var interval = parseInt(root.getAttribute('data-interval'), 10) || 5000;
					var aktif = 0;

					function tampil(idx) {
						var i;
						for (i = 0; i < slides.length; i++) {
							if (i === idx) {
								slides[i].classList.remove('opacity-0', 'pointer-events-none');
								slides[i].classList.add('opacity-100');
							} else {
								slides[i].classList.add('opacity-0', 'pointer-events-none');
								slides[i].classList.remove('opacity-100');
							}
						}
						for (i = 0; i < labels.length; i++) {
							labels[i].classList.toggle('hidden', i !== idx);
						}
						for (i = 0; i < dots.length; i++) {
							dots[i].classList.toggle('w-6', i === idx);
							dots[i].classList.toggle('w-2', i !== idx);
							dots[i].classList.toggle('opacity-100', i === idx);
							dots[i].classList.toggle('opacity-40', i !== idx);
						}
						aktif = idx;
					}

					setInterval(function () {
						tampil((aktif + 1) % slides.length);
					}, interval);
				})();
				</script>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>

<?php
// Card info versi mobile (di luar hero, gaya normal) — memakai $programBerjalan
// yang sudah dihitung di atas (1 baris per program, bukan per level).
?>
<div class="<?= $wadah ?> mt-8 grid gap-3 lg:hidden">
	<div class="card p-4">
		<div class="mb-2 flex items-center justify-between">
			<h2 class="text-sm font-semibold text-ink">Program yang Berjalan</h2>
			<a href="<?= site_url('katalog') ?>" class="text-xs font-semibold text-primary hover:underline">Lihat semua</a>
		</div>
		<div class="space-y-2 text-sm">
			<?php if (!empty($programBerjalan)): ?>
				<?php foreach ($programBerjalan as $pb): ?>
					<div class="flex items-start justify-between gap-2 rounded-xl bg-primary-soft/60 px-3 py-2">
						<div>
							<div class="font-medium text-ink">
								<?= html_escape($pb['kode']) ?> <span class="font-normal text-muted">— <?= html_escape($pb['nama']) ?></span>
							</div>
							<?php if (!empty($pb['cabang'])): ?>
								<div class="text-xs text-muted"><?= html_escape(implode(' · ', $pb['cabang'])) ?></div>
							<?php endif; ?>
						</div>
						<span class="whitespace-nowrap rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700"><?= (int) $pb['jumlah'] ?> kelas</span>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<p class="text-xs text-muted">Belum ada program berjalan.</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="card p-4">
		<div class="mb-2 flex items-center justify-between">
			<h2 class="text-sm font-semibold text-ink">Coming Soon / Gelombang</h2>
			<a href="<?= site_url('gelombang') ?>" class="text-xs font-semibold text-primary hover:underline">Lihat semua</a>
		</div>
		<div class="space-y-2 text-sm">
			<?php if (!empty($gelombang)): ?>
				<?php foreach ($gelombang as $g): ?>
					<div class="rounded-xl border border-primary/20 bg-primary-soft/40 px-3 py-2">
						<div class="flex items-start justify-between gap-2">
							<div>
								<div class="font-medium text-ink"><?= html_escape($g->program_kode) ?> · <?= html_escape($g->angkatan) ?></div>
								<div class="text-xs text-muted"><?= tanggal_indo($g->tgl_buka) ?> s/d <?= tanggal_indo($g->tgl_tutup) ?></div>
							</div>
							<span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs text-primary">Segera</span>
						</div>
						<?php if ($this->session->userdata('logged_in')): ?>
							<a href="<?= site_url('gelombang/daftar/'.$g->id) ?>" class="mt-2 inline-block text-xs font-semibold text-primary hover:underline">Daftar Minat</a>
						<?php else: ?>
							<a href="<?= site_url('auth/login') ?>" class="mt-2 inline-block text-xs font-semibold text-primary hover:underline">Login untuk daftar minat</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<p class="text-xs text-muted">Tidak ada gelombang yang sedang dibuka.</p>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- PINTASAN CEPAT -->
<div class="relative z-10 mx-auto mt-8 max-w-4xl px-4 sm:px-6">
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
