<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Beranda publik: hero editorial (headline + foto utama), section "Cara Mendaftar"
 * (lima kartu langkah), lima kartu program, section Lokasi (carousel cabang),
 * lalu Testimoni.
 *
 * Catatan: kartu info mobile (program berjalan/gelombang), pintasan cepat,
 * blok statistik, section "Metode Kami", dan section "Daftar Kelas" sudah
 * dihapus dari beranda — daftar kelas kini hanya lewat `/katalog` + `/daftar`.
 *
 * Halaman ini memakai `main_penuh` (lihat Beranda::index()) sehingga <main> selebar
 * layar dan tiap section membungkus dirinya sendiri.
 */

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

// TASK 06 — Testimoni.
// Proyek belum punya tabel/CRUD testimoni, sehingga controller belum mengirim
// data apa pun. View ini siap menerima `$testimonis` dari controller tanpa
// perubahan markup: array item (array atau object) dengan field opsional
// nama, isi, foto, rating (1–5), kategori, program, tanggal.
// Item tanpa `isi` diabaikan; tidak ada data karangan di view ini.
$testimonis = (isset($testimonis) && is_array($testimonis)) ? $testimonis : array();
$tesItems = array();
foreach ($testimonis as $t) {
	$t = (array) $t;
	$isi = isset($t['isi']) ? trim((string) $t['isi']) : '';
	if ($isi === '') {
		continue;
	}
	$rating = isset($t['rating']) ? (int) $t['rating'] : 0;
	$tesItems[] = array(
		'nama' => isset($t['nama']) ? trim((string) $t['nama']) : '',
		'isi' => $isi,
		'foto' => ! empty($t['foto']) ? trim((string) $t['foto']) : '',
		'rating' => ($rating >= 1 && $rating <= 5) ? $rating : 0,
		'kategori' => isset($t['kategori']) ? trim((string) $t['kategori']) : '',
		'program' => isset($t['program']) ? trim((string) $t['program']) : '',
		'tanggal' => isset($t['tanggal']) ? trim((string) $t['tanggal']) : '',
	);
}
$tesJumlah = count($tesItems);
?>

<?php
// ---------------------------------------------------------------------------
// PROGRAM (TASK 04) — lima kartu program (section tepat di bawah hero).
// Data ini dipakai dua tempat: section Program dan kartu "Program Unggulan"
// yang bergilir di panel hero — judul (kode) + deskripsi (nama · info) sama.
// Foto memakai aset program yang sudah tersedia di `assets/img/utsmani/`.
// TKIQ & SDTQ belum punya baris di tabel `program` (katalog), jadi tautannya
// jatuh ke halaman katalog; BADAL & BILAL digabung dalam satu kartu.
// ---------------------------------------------------------------------------
$petaProgram = array();
foreach ($programs as $p) {
	$petaProgram[strtoupper($p->kode)] = (int) $p->id;
}
$tautanKatalog = site_url('katalog');
$kartuProgram = array(
	array(
		'kode' => 'TKIQ',
		'nama' => 'Taman Kanak-kanak Islam Qur\'an',
		'info' => 'Usia 4–6 tahun',
		'berkas' => 'Program TKIQ.png',
		'lebar' => 253,
		'tinggi' => 181,
		'ikon' => 'akademik',
		'url' => isset($petaProgram['TKIQ']) ? site_url('katalog/program/'.$petaProgram['TKIQ']) : $tautanKatalog,
	),
	array(
		'kode' => 'SDTQ',
		'nama' => 'Sekolah Dasar Tahfizhul Qur\'an',
		'info' => 'Kelas 1–6',
		'berkas' => 'Program SDTQ.png',
		'lebar' => 240,
		'tinggi' => 181,
		'ikon' => 'buku',
		'url' => isset($petaProgram['SDTQ']) ? site_url('katalog/program/'.$petaProgram['SDTQ']) : $tautanKatalog,
	),
	array(
		'kode' => 'BIHAR',
		'nama' => 'Bimbingan Tahfidz Anak & Remaja',
		'info' => '',
		'berkas' => 'Program Bihar.png',
		'lebar' => 253,
		'tinggi' => 181,
		'ikon' => 'bintang',
		'url' => isset($petaProgram['BIHAR']) ? site_url('katalog/program/'.$petaProgram['BIHAR']) : $tautanKatalog,
	),
	array(
		'kode' => 'PDHQ',
		'nama' => 'Pendidikan Dai Hafidz Qur\'an',
		'info' => '',
		'berkas' => 'Program PDHQ.png',
		'lebar' => 253,
		'tinggi' => 181,
		'ikon' => 'santri',
		'url' => isset($petaProgram['PDHQ']) ? site_url('katalog/program/'.$petaProgram['PDHQ']) : $tautanKatalog,
	),
	array(
		'kode' => 'BADAL / BILAL',
		'nama' => 'Bimbingan Al-Qur\'an Dewasa',
		'info' => '',
		'berkas' => 'Program BADAL.png',
		'lebar' => 253,
		'tinggi' => 181,
		'ikon' => 'lembaga',
		'url' => $tautanKatalog,
	),
);
$ikonProgram = array(
	'akademik' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443"/>',
	'buku' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
	'bintang' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>',
	'santri' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
	'lembaga' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>',
);
?>

<!-- HERO EDITORIAL — headline kiri/kanan di atas, foto tengah, panel coklat di bawah -->
<?php
$fotoHero = hero_foto();
$judulBersama = hero_aset('Bersama quran.svg');
$judulBahagia = hero_aset('Kita lebih bahagia.svg');
$jumlahPeserta = isset($jumlah_peserta) ? (int) $jumlah_peserta : 0;

// ---------------------------------------------------------------------------
// Kartu "Program Unggulan" di panel hero — bergilir otomatis antar program.
// Sumber teksnya array yang sama dengan section Program (`$kartuProgram`),
// jadi judul (kode: TKIQ, SDTQ, BIHAR, ...) dan deskripsinya (nama + info)
// persis sama dengan kartu di section itu — tidak ada teks terpisah yang
// bisa melenceng. CTA memakai tautan kartu yang sama.
// ---------------------------------------------------------------------------
$programUnggulan = array();
foreach ($kartuProgram as $kartu) {
	$programUnggulan[] = array(
		'kode' => $kartu['kode'],
		'nama' => $kartu['nama'],
		'info' => $kartu['info'],
		'url' => $kartu['url'],
		'img' => hero_aset($kartu['berkas']),
	);
}
$hpPertama = $programUnggulan[0];
?>
<section class="au-home-hero" aria-labelledby="auHomeHeroTitle">
	<div class="au-home-container">
		<div class="au-home-hero__panggung">
			<div class="au-home-hero__headline">
				<h1 class="au-home-hero__judul" id="auHomeHeroTitle">
					<span class="au-home-hero__judul-kiri">
						<?php if ($judulBersama): ?>
							<img src="<?= html_escape($judulBersama) ?>" alt="Bersama Al-Qur'an" width="394" height="167" fetchpriority="high" decoding="async">
						<?php else: ?>
							Bersama Al-Qur'an
						<?php endif; ?>
					</span>
					<span class="au-home-hero__judul-kanan">
						<?php if ($judulBahagia): ?>
							<img src="<?= html_escape($judulBahagia) ?>" alt="Kita Lebih Bahagia" width="375" height="171" fetchpriority="high" decoding="async">
						<?php else: ?>
							Kita Lebih Bahagia
						<?php endif; ?>
					</span>
				</h1>
			</div>

			<?php if ($fotoHero): ?>
				<figure class="au-home-hero__foto">
					<img src="<?= html_escape($fotoHero) ?>" alt="Ilustrasi suasana belajar Al-Qur'an di LBQ Al-Utsmani" width="434" height="726" fetchpriority="high" decoding="async">
				</figure>
			<?php endif; ?>

			<div class="au-home-hero__panel">
				<div class="au-home-hero__panel-kiri">
					<p class="au-home-eyebrow au-home-eyebrow--light">Metode Al-Utsmani</p>

					<h2 class="au-home-hero__panel-title">Belajar Qur'an dengan Metode Utsmani</h2>

					<p class="au-home-hero__panel-desc">
						Perbaiki kualitas bacaan Al-Qur'an Anda bersama Utsmani melalui bimbingan guru dan metode pembelajaran yang terarah.
					</p>

					<div class="au-home-hero__actions">
						<a class="au-home-button au-home-button--cream" href="<?= site_url('daftar') ?>">
							Pilih Program
							<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6-6m6 6l-6 6"/></svg>
						</a>
						<a class="au-home-button au-home-button--outline-light" href="#cara-mendaftar">Cara Mendaftar</a>
					</div>

					<?php if (empty($user)): ?>
						<p class="au-home-caption au-home-caption--light au-home-hero__login">
							Sudah punya akun? <a href="<?= site_url('auth/login') ?>">Masuk di sini</a>
						</p>
					<?php endif; ?>

					<div class="au-home-hero__peserta">
						<span class="au-home-hero__peserta-icon" aria-hidden="true">
							<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
						</span>
						<p class="au-home-hero__peserta-teks">
							<?php if ($jumlahPeserta > 0): ?>
								<strong><?= number_format($jumlahPeserta, 0, ',', '.') ?></strong> peserta terdaftar dari <strong><?= (int) $jumlah_cabang ?></strong> cabang aktif
							<?php else: ?>
								<strong>Peserta</strong> dari berbagai cabang di Indonesia
							<?php endif; ?>
						</p>
					</div>
				</div>

				<div class="au-home-hero__panel-kanan">
					<ul class="au-home-hero__keunggulan">
						<li>
							<span class="au-home-hero__ikon" aria-hidden="true">
								<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
							</span>
							<h3>Bertahap dan Terstruktur</h3>
						</li>
						<li>
							<span class="au-home-hero__ikon" aria-hidden="true">
								<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9h.008v.008H9V9zm6 0h.008v.008H15V9z"/></svg>
							</span>
							<h3>Menyenangkan dan Interaktif</h3>
						</li>
						<li>
							<span class="au-home-hero__ikon" aria-hidden="true">
								<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
							</span>
							<h3>Evaluasi Berkala</h3>
						</li>
						<li>
							<span class="au-home-hero__ikon" aria-hidden="true">
								<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
							</span>
							<h3>Guru Bersertifikat</h3>
						</li>
					</ul>

					<?php
					// Kartu "Program Unggulan" bergilir: satu <img> dipakai bersama,
					// teks tiap program ditumpuk di satu sel grid lalu di-cross-fade.
					// Tanpa JS hanya slide pertama yang tampil (lihat `.is-aktif` di CSS).
					?>
					<article class="au-home-hero__program" data-hp>
						<div class="au-home-hero__program-media">
							<div class="au-home-hero__program-gambar">
								<?php if ($hpPertama['img']): ?>
									<img src="<?= html_escape($hpPertama['img']) ?>" alt="Program <?= html_escape($hpPertama['kode']) ?> — <?= html_escape($hpPertama['nama']) ?>" width="240" height="181" decoding="async" data-hp-img>
								<?php else: ?>
									<img alt="" width="240" height="181" decoding="async" data-hp-img hidden>
								<?php endif; ?>
								<span class="au-home-hero__program-fallback" data-hp-fallback aria-hidden="true"<?= $hpPertama['img'] ? ' hidden' : '' ?>><?= html_escape($hpPertama['kode']) ?></span>
							</div>
							<div class="au-home-hero__program-nav" role="group" aria-label="Pilih program unggulan" hidden>
								<?php foreach ($programUnggulan as $i => $hp): ?>
									<button type="button" class="au-home-hero__program-dot<?= $i === 0 ? ' is-aktif' : '' ?>" data-hp-dot="<?= $i ?>" aria-label="Tampilkan <?= html_escape($hp['kode']) ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?>></button>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="au-home-hero__program-slides">
							<?php foreach ($programUnggulan as $i => $hp): ?>
								<div class="au-home-hero__program-body<?= $i === 0 ? ' is-aktif' : '' ?>" data-hp-slide="<?= $i ?>" data-kode="<?= html_escape($hp['kode']) ?>" data-img="<?= html_escape((string) $hp['img']) ?>" data-alt="Program <?= html_escape($hp['kode']) ?> — <?= html_escape($hp['nama']) ?>"<?= $i === 0 ? '' : ' aria-hidden="true"' ?>>
									<span class="au-home-hero__program-badge">Program Unggulan</span>
									<h3><?= html_escape($hp['kode']) ?></h3>
									<p><?= html_escape($hp['nama']) ?><?= $hp['info'] !== '' ? ' · '.html_escape($hp['info']) : '' ?></p>
									<a class="au-home-hero__program-cta" href="<?= html_escape($hp['url']) ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>>
										Lihat program
										<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6-6m6 6l-6 6"/></svg>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</article>

					<?php if (! empty($programBerjalan) || ! empty($gelombang)): ?>
						<div class="au-home-hero__meta">
							<?php if (! empty($programBerjalan)): ?>
								<?php
								$kodeBerjalan = array();
								foreach ($programBerjalan as $pb) {
									$kodeBerjalan[] = $pb['kode'];
								}
								?>
								<p>
									<span class="au-home-hero__meta-dot" aria-hidden="true"></span>
									Program berjalan: <strong><?= html_escape(implode(' · ', $kodeBerjalan)) ?></strong>
									<a href="<?= site_url('katalog') ?>">Lihat program</a>
								</p>
							<?php endif; ?>
							<?php if (! empty($gelombang)): ?>
								<?php $g0 = $gelombang[0]; ?>
								<p>
									<span class="au-home-hero__meta-dot" aria-hidden="true"></span>
									Gelombang <strong><?= html_escape($g0->program_kode) ?><?= ! empty($g0->angkatan) ? ' '.html_escape($g0->angkatan) : '' ?></strong> dibuka
									<a href="<?= site_url('gelombang') ?>">Lihat gelombang</a>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
/* Rotator kartu "Program Unggulan" di panel hero.
   - Teks tiap program sudah ditumpuk di markup (tanpa JS hanya slide 1 tampil).
   - Satu <img> dipakai bersama; src diganti mengikuti slide aktif.
   - Berhenti sebentar saat kursor/fokus di kartu atau tab tidak aktif.
   - `prefers-reduced-motion: reduce` → tanpa rotasi otomatis (titik tetap bisa diklik). */
(function () {
	var kartu = document.querySelector('[data-hp]');
	if (! kartu) { return; }

	var gambar = kartu.querySelector('[data-hp-img]');
	var cadangan = kartu.querySelector('[data-hp-fallback]');
	var nav = kartu.querySelector('.au-home-hero__program-nav');
	var slides = Array.prototype.slice.call(kartu.querySelectorAll('[data-hp-slide]'));
	var titik = Array.prototype.slice.call(kartu.querySelectorAll('[data-hp-dot]'));
	if (slides.length < 2) { return; }
	if (nav) { nav.hidden = false; }

	var JEDA = 5200;
	var halus = ! window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var kini = 0;
	var timer = null;
	var sudahMuat = {};

	function praMuat(i) {
		var src = slides[i].getAttribute('data-img');
		if (! src || sudahMuat[src]) { return; }
		sudahMuat[src] = true;
		var g = new Image();
		g.src = src;
	}

	function tampil(i) {
		kini = (i % slides.length + slides.length) % slides.length;

		slides.forEach(function (s, n) {
			var aktif = (n === kini);
			s.classList.toggle('is-aktif', aktif);
			s.setAttribute('aria-hidden', aktif ? 'false' : 'true');
			var tautan = s.querySelector('.au-home-hero__program-cta');
			if (tautan) { tautan.tabIndex = aktif ? 0 : -1; }
		});

		titik.forEach(function (t, n) {
			var aktif = (n === kini);
			t.classList.toggle('is-aktif', aktif);
			if (aktif) { t.setAttribute('aria-current', 'true'); }
			else { t.removeAttribute('aria-current'); }
		});

		var slide = slides[kini];
		var src = slide.getAttribute('data-img') || '';

		if (gambar) {
			if (src) {
				if (gambar.getAttribute('src') !== src) { gambar.setAttribute('src', src); }
				gambar.hidden = false;
				gambar.setAttribute('alt', slide.getAttribute('data-alt') || '');
			} else {
				gambar.hidden = true;
			}
		}
		if (cadangan) {
			cadangan.textContent = slide.getAttribute('data-kode') || '';
			cadangan.hidden = (src !== '');
		}

		praMuat((kini + 1) % slides.length);
	}

	function hentikan() {
		if (timer) { window.clearInterval(timer); timer = null; }
	}

	function mulai() {
		hentikan();
		if (! halus || document.hidden) { return; }
		timer = window.setInterval(function () { tampil(kini + 1); }, JEDA);
	}

	titik.forEach(function (t, n) {
		t.addEventListener('click', function () {
			tampil(n);
			mulai();
		});
	});

	kartu.addEventListener('mouseenter', hentikan);
	kartu.addEventListener('mouseleave', mulai);
	kartu.addEventListener('focusin', hentikan);
	kartu.addEventListener('focusout', function (e) {
		if (! kartu.contains(e.relatedTarget)) { mulai(); }
	});
	document.addEventListener('visibilitychange', function () {
		if (document.hidden) { hentikan(); } else { mulai(); }
	});

	tampil(0);
	mulai();
})();
</script>

<?php
// ---------------------------------------------------------------------------
// CARA MENDAFTAR (TASK 08) — lima kartu editorial, tepat di bawah hero.
//
// Menggantikan panel ringkas TASK 07 (satu panel berisi lima kolom teks).
// Komposisi baru: header di tengah, lalu lima kartu — kartu 01 gradient caramel
// sebagai highlight, kartu 02–05 cream hangat. Nomor besar di kiri atas,
// ilustrasi inline SVG di kanan atas, judul + deskripsi di bawah, ornamen
// geometris di sudut kanan bawah. Semua ilustrasi SVG lokal (tanpa library,
// tanpa emoji, tanpa gambar baru).
//
// Isi langkah mengikuti alur yang benar-benar berlaku di sistem: pilih
// cabang + program → pilih kelas → registrasi (akun + peserta, unggah bukti
// bila kelas berbiaya) → verifikasi admin (pembayaran + penempatan level) →
// pendaftaran aktif, mulai belajar. Anchor `#cara-mendaftar` dipertahankan.
// ---------------------------------------------------------------------------
$langkahDaftar = array(
	array(
		'judul' => 'Pilih Program',
		'desc' => 'Tentukan cabang dan program belajar yang sesuai dengan kebutuhanmu.',
		'ilustrasi' => '<svg class="au-reg-card__ill" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
			.'<rect x="7" y="46" width="50" height="10" rx="5" class="ill-mid"/>'
			.'<rect x="11" y="36" width="42" height="10" rx="5" class="ill-strong"/>'
			.'<rect x="7" y="20" width="50" height="16" rx="6" class="ill-strong"/>'
			.'<rect x="7" y="20" width="10" height="16" rx="5" class="ill-mid"/>'
			.'<path d="M42 24.6l4.6 3.4-4.6 3.4-4.6-3.4z" class="ill-soft"/>'
			.'</svg>',
	),
	array(
		'judul' => 'Pilih Kelas',
		'desc' => 'Sesuaikan kelas dengan jadwal yang tersedia.',
		'ilustrasi' => '<svg class="au-reg-card__ill" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
			.'<rect x="8" y="15" width="48" height="43" rx="10" class="ill-soft"/>'
			.'<path d="M8 25a10 10 0 0 1 10-10h28a10 10 0 0 1 10 10v4H8z" class="ill-strong"/>'
			.'<rect x="17" y="7" width="6" height="13" rx="3" class="ill-mid"/>'
			.'<rect x="41" y="7" width="6" height="13" rx="3" class="ill-mid"/>'
			.'<circle cx="21" cy="38" r="3.2" class="ill-mid"/>'
			.'<circle cx="32" cy="38" r="3.2" class="ill-mid"/>'
			.'<circle cx="43" cy="38" r="3.2" class="ill-mid"/>'
			.'<circle cx="21" cy="48" r="3.2" class="ill-ink"/>'
			.'<circle cx="32" cy="48" r="3.2" class="ill-mid"/>'
			.'<circle cx="43" cy="48" r="3.2" class="ill-mid"/>'
			.'</svg>',
	),
	array(
		'judul' => 'Isi Data',
		'desc' => 'Lengkapi formulir pendaftaran dengan data yang benar.',
		'ilustrasi' => '<svg class="au-reg-card__ill" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
			.'<rect x="9" y="7" width="34" height="49" rx="7" class="ill-soft"/>'
			.'<rect x="16" y="15" width="13" height="4.5" rx="2.25" class="ill-strong"/>'
			.'<rect x="16" y="25" width="20" height="3" rx="1.5" class="ill-mid"/>'
			.'<rect x="16" y="32" width="16" height="3" rx="1.5" class="ill-mid"/>'
			.'<rect x="16" y="39" width="19" height="3" rx="1.5" class="ill-mid"/>'
			.'<rect x="16" y="46" width="9" height="3" rx="1.5" class="ill-mid"/>'
			.'<g transform="rotate(40 44 40)">'
			.'<rect x="39.5" y="17" width="9" height="29" rx="4.5" class="ill-strong"/>'
			.'<path d="M39.5 43h9L44 53z" class="ill-mid"/>'
			.'<rect x="39.5" y="23" width="9" height="3.4" class="ill-soft"/>'
			.'</g>'
			.'</svg>',
	),
	array(
		'judul' => 'Verifikasi',
		'desc' => 'Tunggu konfirmasi admin atas pembayaran dan penempatan levelmu.',
		'ilustrasi' => '<svg class="au-reg-card__ill" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
			.'<path d="M32 5l21 7.4v15.3C53 41 44.4 50.9 32 54.7 19.6 50.9 11 41 11 27.7V12.4z" class="ill-mid"/>'
			.'<path d="M32 12.5l15 5.3v10.4c0 9.4-6 16.8-15 20.1-9-3.3-15-10.7-15-20.1V17.8z" class="ill-soft"/>'
			.'<path d="M24.4 31.6l5.4 5.4 10-10.4" class="ill-check"/>'
			.'</svg>',
	),
	array(
		'judul' => 'Mulai Belajar',
		'desc' => 'Selamat bergabung! Saatnya belajar Al-Qur\'an bersama kami.',
		'ilustrasi' => '<svg class="au-reg-card__ill" viewBox="0 0 64 64" aria-hidden="true" focusable="false">'
			.'<path d="M32 17.5c-6-4-13-5.6-20-5.2v33c7-.4 14 1.2 20 5.2z" class="ill-soft"/>'
			.'<path d="M32 17.5c6-4 13-5.6 20-5.2v33c-7-.4-14 1.2-20 5.2z" class="ill-soft"/>'
			.'<path d="M32 17.5v33" class="ill-spine"/>'
			.'<path d="M17 24.5c4 .2 8 1.2 11 2.9M17 31c4 .2 8 1.2 11 2.9M17 37.5c4 .2 8 1.2 11 2.9" class="ill-line"/>'
			.'<path d="M47 24.5c-4 .2-8 1.2-11 2.9M47 31c-4 .2-8 1.2-11 2.9M47 37.5c-4 .2-8 1.2-11 2.9" class="ill-line"/>'
			.'</svg>',
	),
);

// Ornamen kartu: satu komponen (bintang geometris 8 sudut) dipakai kelima kartu.
$ornamenKartu = '<svg class="au-reg-card__orn" viewBox="0 0 100 100" aria-hidden="true" focusable="false">'
	.'<g fill="none" stroke="currentColor" stroke-width="2">'
	.'<rect x="18" y="18" width="64" height="64" rx="3"/>'
	.'<rect x="18" y="18" width="64" height="64" rx="3" transform="rotate(45 50 50)"/>'
	.'<circle cx="50" cy="50" r="17"/>'
	.'<circle cx="50" cy="50" r="7"/>'
	.'</g></svg>';
?>
<section class="au-reg" id="cara-mendaftar" aria-labelledby="auRegTitle">
	<div class="au-reg__deco" aria-hidden="true">
		<svg class="au-reg__deco-kubah" viewBox="0 0 240 240" fill="currentColor" focusable="false">
			<path d="M120 34C150 56 166 84 166 114L166 120L186 120C176 142 170 165 170 190L170 222L70 222L70 190C70 165 64 142 54 120L74 120L74 114C74 84 90 56 120 34Z"/>
			<rect x="117" y="20" width="6" height="16" rx="3"/>
			<circle cx="120" cy="16" r="6"/>
		</svg>
		<svg class="au-reg__deco-pola" viewBox="0 0 200 200" fill="none" stroke="currentColor" focusable="false">
			<g stroke-width="2.5">
				<rect x="42" y="42" width="116" height="116" rx="4"/>
				<rect x="42" y="42" width="116" height="116" rx="4" transform="rotate(45 100 100)"/>
				<circle cx="100" cy="100" r="36"/>
				<circle cx="100" cy="100" r="14"/>
			</g>
		</svg>
		<svg class="au-reg__deco-lengkung" viewBox="0 0 900 200" preserveAspectRatio="none" fill="none" stroke="currentColor" focusable="false">
			<path d="M-40 150C160 40 340 40 540 150s340 110 540 0" stroke-width="3"/>
			<path d="M-40 192C160 82 340 82 540 192s340 110 540 0" stroke-width="2"/>
		</svg>
	</div>
	<div class="au-home-container">
		<div class="au-reg__head">
			<span class="au-reg__label">Panduan Pendaftaran</span>
			<h2 class="au-reg__title" id="auRegTitle">Cara Bergabung dengan Al-Utsmani</h2>
			<p class="au-reg__desc">Ikuti lima langkah mudah untuk memulai perjalanan belajar Al-Qur'an bersama Al-Utsmani.</p>
		</div>
		<ol class="au-reg__cards">
			<?php foreach ($langkahDaftar as $i => $langkah): ?>
				<li class="au-reg-card<?= $i === 0 ? ' au-reg-card--highlight' : '' ?>">
					<span class="au-reg-card__num" aria-hidden="true"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
					<?= $langkah['ilustrasi'] ?>

					<?= $ornamenKartu ?>

					<div class="au-reg-card__body">
						<h3 class="au-reg-card__title"><?= html_escape($langkah['judul']) ?></h3>
						<p class="au-reg-card__desc"><?= html_escape($langkah['desc']) ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="au-prog" id="program" aria-labelledby="auProgTitle">
	<div class="au-home-container">
		<div class="au-prog__head">
			<span class="au-prog__label">Program Kami</span>
			<h2 class="au-prog__title" id="auProgTitle">Pilih Program Sesuai Usia &amp; Kebutuhan</h2>
			<p class="au-prog__desc">Berbagai program pembelajaran Al-Qur'an yang terarah, bertahap, dan berkelanjutan.</p>
		</div>

		<div class="au-prog__grid">
			<?php foreach ($kartuProgram as $kartu): ?>
				<?php $fotoKartu = hero_aset($kartu['berkas']); ?>
				<a class="au-prog-card" href="<?= html_escape($kartu['url']) ?>">
					<div class="au-prog-card__media">
						<?php if ($fotoKartu): ?>
							<img src="<?= html_escape($fotoKartu) ?>" alt="Program <?= html_escape($kartu['kode']) ?> — <?= html_escape($kartu['nama']) ?>" width="<?= (int) $kartu['lebar'] ?>" height="<?= (int) $kartu['tinggi'] ?>" loading="lazy" decoding="async">
						<?php endif; ?>
					</div>
					<span class="au-prog-card__icon" aria-hidden="true">
						<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><?= $ikonProgram[$kartu['ikon']] ?></svg>
					</span>
					<div class="au-prog-card__body">
						<h3 class="au-prog-card__name"><?= html_escape($kartu['kode']) ?></h3>
						<p class="au-prog-card__desc"><?= html_escape($kartu['nama']) ?><?= $kartu['info'] !== '' ? ' · '.html_escape($kartu['info']) : '' ?></p>
						<span class="au-prog-card__arrow" aria-hidden="true">
							<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6-6m6 6l-6 6"/></svg>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="au-prog__more">
			<a class="au-prog__more-btn" href="<?= html_escape($tautanKatalog) ?>">
				Lihat semua Program
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6-6m6 6l-6 6"/></svg>
			</a>
		</div>
	</div>
</section>

<?php
// ---------------------------------------------------------------------------
// LOKASI (TASK 05) — carousel cabang, tepat di bawah section Program.
//
// Sumber data (tidak ada data baru/karangan):
// - `$cabangs` = Cabang_model::aktif() → 12 cabang aktif, kolom `nama`,
//   `wilayah`, `status`. Skema `cabang` TIDAK punya alamat/koordinat/URL
//   Google Maps, jadi baris lokasi menampilkan `wilayah` (bukan alamat fiktif).
// - Program per cabang diturunkan dari `$rekap` (Kelas_model::rekapProgram())
//   sehingga hanya program yang benar-benar punya kelas AKTIF di cabang itu
//   yang tampil; cabang tanpa kelas aktif tidak diberi chip program.
// - Foto: cabang_gambar($wilayah) → satu aset per wilayah (ilustrasi wilayah,
//   bukan dokumentasi cabang) + fallback gradient bila berkas tidak ada.
// ---------------------------------------------------------------------------
$kodeProgram = array();
foreach ($programs as $p) {
	$kodeProgram[(int) $p->id] = $p->kode;
}
$programPerCabang = array();
foreach ($rekap as $pid => $info) {
	if (empty($info['cabang'])) {
		continue;
	}
	foreach ($info['cabang'] as $cid => $c) {
		if (isset($kodeProgram[$pid])) {
			$programPerCabang[(int) $cid][] = $kodeProgram[$pid];
		}
	}
}
// Posisi fokus foto per wilayah (hasil analisis pusat detail gambar, supaya
// subjek/bangunan tidak terpotong saat crop `object-fit: cover`).
$posisiFotoCabang = array(
	'Jakarta Timur' => '50% 40%',
	'Bekasi' => '50% 45%',
	'Cirebon' => '50% 58%',
	'Padang' => '57% 60%',
	'Bogor' => '50% 40%',
	'Sorong' => '50% 58%',
);
?>
<section class="au-lok" id="lokasi" aria-labelledby="auLokTitle" data-lok>
	<div class="au-home-container">
		<div class="au-lok__head">
			<div class="au-lok__intro">
				<span class="au-lok__label">Lokasi Kami</span>
				<h2 class="au-lok__title" id="auLokTitle">Temukan Cabang Al-Utsmani Terdekat</h2>
				<p class="au-lok__desc">Temukan lokasi pembelajaran Al-Qur'an Al-Utsmani dan pilih cabang yang paling sesuai dengan kebutuhanmu.</p>
			</div>
			<div class="au-lok__nav" data-lok-nav>
				<button type="button" class="au-lok__nav-btn" data-lok-prev aria-label="Cabang sebelumnya" aria-controls="auLokTrack">
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
				</button>
				<button type="button" class="au-lok__nav-btn" data-lok-next aria-label="Cabang berikutnya" aria-controls="auLokTrack">
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
				</button>
			</div>
		</div>
	</div>

	<div class="au-lok__viewport">
		<div class="au-lok__track" id="auLokTrack" data-lok-track tabindex="0" role="group" aria-label="Carousel cabang Al-Utsmani">
			<?php foreach ($cabangs as $c): ?>
				<?php
				$fotoCabang = cabang_gambar($c->wilayah);
				$posisiFoto = isset($posisiFotoCabang[$c->wilayah]) ? $posisiFotoCabang[$c->wilayah] : 'center 45%';
				$programCabang = isset($programPerCabang[(int) $c->id]) ? $programPerCabang[(int) $c->id] : array();
				?>
				<article class="au-lok-card">
					<div class="au-lok-card__media">
						<?php if ($fotoCabang): ?>
							<img src="<?= html_escape($fotoCabang) ?>" alt="Ilustrasi wilayah <?= html_escape($c->wilayah) ?>" loading="lazy" decoding="async" width="960" height="640" style="--au-lok-pos: <?= html_escape($posisiFoto) ?>">
						<?php else: ?>
							<span class="au-lok-card__fallback" aria-hidden="true"><?= html_escape(singkatan($c->wilayah)) ?></span>
						<?php endif; ?>
						<span class="au-lok-card__badge"><?= $c->status === 'aktif' ? 'Aktif' : 'Nonaktif' ?></span>
					</div>
					<div class="au-lok-card__body">
						<h3 class="au-lok-card__name"><?= html_escape($c->nama) ?></h3>
						<p class="au-lok-card__wilayah">
							<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
							<span>Wilayah <?= html_escape($c->wilayah) ?></span>
						</p>
						<div class="au-lok-card__program">
							<?php if ($programCabang): ?>
								<span class="au-lok-card__program-label">Program</span>
								<div class="au-lok-card__chips">
									<?php foreach ($programCabang as $kodeCabang): ?>
										<span class="au-lok-chip"><?= html_escape($kodeCabang) ?></span>
									<?php endforeach; ?>
								</div>
							<?php else: ?>
								<span class="au-lok-card__program-label">Program</span>
								<span class="au-lok-card__kosong">Belum ada kelas aktif</span>
							<?php endif; ?>
						</div>
						<a class="au-lok-card__cta" href="<?= site_url('daftar/kelas?cabang='.(int) $c->id) ?>">
							<span>Lihat Kelas</span>
							<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6-6m6 6l-6 6"/></svg>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="au-home-container">
		<p class="au-lok__note">Foto bersifat ilustrasi wilayah, bukan dokumentasi cabang.</p>
		<div class="au-lok__semua">
			<a class="au-home-button au-home-button-primary au-lok__semua-btn" href="<?= site_url('daftar') ?>">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
				<span>Lihat Semua Cabang dan Lokasi</span>
			</a>
		</div>
	</div>
</section>

<script>
(function () {
	var root = document.querySelector('[data-lok]');
	if (!root) { return; }
	var track = root.querySelector('[data-lok-track]');
	var nav = root.querySelector('[data-lok-nav]');
	var prev = root.querySelector('[data-lok-prev]');
	var next = root.querySelector('[data-lok-next]');
	if (!track || !prev || !next) { return; }

	var cards = Array.prototype.slice.call(track.children);
	var halus = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function perilaku() { return halus ? 'smooth' : 'auto'; }
	function langkah() {
		if (cards.length < 2) { return cards.length ? cards[0].getBoundingClientRect().width : 0; }
		return cards[1].getBoundingClientRect().left - cards[0].getBoundingClientRect().left;
	}
	function bisaGeser() { return track.scrollWidth - track.clientWidth > 2; }
	function perbarui() {
		var geser = bisaGeser();
		if (nav) { nav.hidden = !geser; }
		prev.disabled = !geser || track.scrollLeft <= 2;
		next.disabled = !geser || track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
	}
	function geser(arah) {
		var s = langkah() * arah;
		if (!s) { return; }
		track.scrollBy({ left: s, behavior: perilaku() });
	}

	prev.addEventListener('click', function () { geser(-1); });
	next.addEventListener('click', function () { geser(1); });

	track.addEventListener('keydown', function (e) {
		if (e.key === 'ArrowLeft') { e.preventDefault(); geser(-1); }
		else if (e.key === 'ArrowRight') { e.preventDefault(); geser(1); }
		else if (e.key === 'Home') { e.preventDefault(); track.scrollTo({ left: 0, behavior: perilaku() }); }
		else if (e.key === 'End') { e.preventDefault(); track.scrollTo({ left: track.scrollWidth, behavior: perilaku() }); }
	});

	var tunda = null;
	track.addEventListener('scroll', function () {
		window.clearTimeout(tunda);
		tunda = window.setTimeout(perbarui, 70);
	}, { passive: true });

	// Drag mouse; sentuhan memakai scroll native (swipe) bawaan browser.
	var aktif = false, menyeret = false, xAwal = 0, kiriAwal = 0, jarakAkhir = 0;
	track.addEventListener('dragstart', function (e) { e.preventDefault(); });
	track.addEventListener('pointerdown', function (e) {
		if (e.pointerType !== 'mouse' || e.button !== 0) { return; }
		aktif = true; menyeret = false; xAwal = e.clientX; kiriAwal = track.scrollLeft; jarakAkhir = 0;
	});
	window.addEventListener('pointermove', function (e) {
		if (!aktif) { return; }
		var d = e.clientX - xAwal;
		if (!menyeret && Math.abs(d) > 6) { menyeret = true; }
		if (!menyeret) { return; }
		track.scrollLeft = kiriAwal - d;
		jarakAkhir = d;
	});
	function lepas() {
		if (!aktif) { return; }
		aktif = false;
		if (menyeret) {
			var s = langkah();
			if (s) {
				track.scrollTo({ left: Math.round(track.scrollLeft / s) * s, behavior: perilaku() });
			}
			perbarui();
		}
	}
	window.addEventListener('pointerup', lepas);
	window.addEventListener('pointercancel', lepas);
	track.addEventListener('click', function (e) {
		if (menyeret || Math.abs(jarakAkhir) > 6) { e.preventDefault(); e.stopPropagation(); }
		menyeret = false; jarakAkhir = 0;
	}, true);

	if (window.ResizeObserver) { new ResizeObserver(perbarui).observe(track); }
	window.addEventListener('resize', perbarui);
	perbarui();
})();
</script>

<!-- TESTIMONI — data dari controller ($testimonis); kosong → empty state -->
<section class="au-tes" aria-labelledby="auTesTitle" data-tes>
	<div class="au-home-container">
		<div class="au-tes__head">
			<div class="au-tes__intro">
				<span class="au-tes__label">Cerita Mereka</span>
				<h2 class="au-tes__title" id="auTesTitle">Apa Kata Mereka?</h2>
				<p class="au-tes__desc">Pengalaman peserta dan orang tua dalam belajar Al-Qur'an bersama Al-Utsmani.</p>
			</div>
			<div class="au-tes__nav" data-tes-nav hidden>
				<button type="button" class="au-tes__nav-btn" data-tes-prev aria-label="Testimoni sebelumnya" aria-controls="auTesTrack">
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
				</button>
				<button type="button" class="au-tes__nav-btn" data-tes-next aria-label="Testimoni berikutnya" aria-controls="auTesTrack">
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
				</button>
			</div>
		</div>
	</div>

	<div class="au-tes__stage">
		<?php if ($tesJumlah > 0): ?>
			<div class="au-tes__viewport">
				<div class="au-tes__track" id="auTesTrack" data-tes-track tabindex="0" role="group" aria-label="Carousel testimoni Al-Utsmani">
					<?php foreach ($tesItems as $t): ?>
						<?php
						$fotoUrl = '';
						if ($t['foto'] !== '') {
							$fotoUrl = preg_match('#^https?://#i', $t['foto']) ? $t['foto'] : base_url($t['foto']);
						}
						?>
						<article class="au-tes-card">
							<div class="au-tes-card__atas">
								<svg class="au-tes-card__quote" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/>
									<path stroke-linecap="round" stroke-linejoin="round" d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/>
								</svg>
								<?php if ($t['rating'] > 0): ?>
									<span class="au-tes-card__rating" role="img" aria-label="Rating <?= (int) $t['rating'] ?> dari 5">
										<?php for ($b = 1; $b <= 5; $b++): ?>
											<svg class="au-tes-card__star<?= $b > $t['rating'] ? ' au-tes-card__star--kosong' : '' ?>" viewBox="0 0 24 24" aria-hidden="true" fill="<?= $b <= $t['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
										<?php endfor; ?>
									</span>
								<?php endif; ?>
							</div>
							<p class="au-tes-card__isi" data-tes-isi><?= nl2br(html_escape($t['isi'])) ?></p>
							<button type="button" class="au-tes-card__baca" data-tes-baca aria-expanded="false" hidden>Baca Selengkapnya</button>
							<div class="au-tes-card__bawah">
								<div class="au-tes-card__orang">
									<?php if ($fotoUrl !== ''): ?>
										<img class="au-tes-card__avatar" src="<?= html_escape($fotoUrl) ?>" alt="Foto <?= html_escape($t['nama'] !== '' ? $t['nama'] : 'pemberi testimoni') ?>" width="48" height="48" loading="lazy" decoding="async">
									<?php else: ?>
										<span class="au-tes-card__avatar au-tes-card__avatar--inisial" aria-hidden="true">
											<?php if ($t['nama'] !== ''): ?>
												<?= html_escape(singkatan($t['nama'], 2)) ?>
											<?php else: ?>
												<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><circle cx="12" cy="8" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 0 0-16 0"/></svg>
											<?php endif; ?>
										</span>
									<?php endif; ?>
									<span class="au-tes-card__identitas">
										<?php if ($t['nama'] !== ''): ?>
											<span class="au-tes-card__nama"><?= html_escape($t['nama']) ?></span>
										<?php endif; ?>
										<?php if ($t['kategori'] !== '' || $t['program'] !== '' || $t['tanggal'] !== ''): ?>
											<span class="au-tes-card__meta">
												<?php if ($t['kategori'] !== ''): ?><span class="au-tes-card__kategori"><?= html_escape($t['kategori']) ?></span><?php endif; ?>
												<?php if ($t['program'] !== ''): ?><span class="au-tes-card__program"><?= html_escape($t['program']) ?></span><?php endif; ?>
												<?php if ($t['tanggal'] !== ''): ?><time class="au-tes-card__tanggal" datetime="<?= html_escape($t['tanggal']) ?>"><?= html_escape(tanggal_indo($t['tanggal'])) ?></time><?php endif; ?>
											</span>
										<?php endif; ?>
									</span>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php else: ?>
			<div class="au-home-container">
				<div class="au-tes__empty">
					<span class="au-tes__empty-ikon" aria-hidden="true">
						<svg fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
							<path stroke-linecap="round" stroke-linejoin="round" d="M8 12a2 2 0 0 0 2-2V8H8"/>
							<path stroke-linecap="round" stroke-linejoin="round" d="M14 12a2 2 0 0 0 2-2V8h-2"/>
						</svg>
					</span>
					<p class="au-tes__empty-teks">Cerita dan pengalaman peserta akan hadir di sini.</p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<script>
(function () {
	var root = document.querySelector('[data-tes]');
	if (!root) { return; }

	var track = root.querySelector('[data-tes-track]');
	var nav = root.querySelector('[data-tes-nav]');
	var prev = root.querySelector('[data-tes-prev]');
	var next = root.querySelector('[data-tes-next]');
	var halus = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function perilaku() { return halus ? 'smooth' : 'auto'; }
	function langkah() {
		if (!track) { return 0; }
		var kartu = Array.prototype.slice.call(track.children);
		if (kartu.length < 2) { return kartu.length ? kartu[0].getBoundingClientRect().width : 0; }
		return kartu[1].getBoundingClientRect().left - kartu[0].getBoundingClientRect().left;
	}
	function bisaGeser() { return !!track && track.scrollWidth - track.clientWidth > 2; }
	function perbarui() {
		if (!track || !nav || !prev || !next) { return; }
		var geser = bisaGeser();
		nav.hidden = !geser;
		prev.disabled = !geser || track.scrollLeft <= 2;
		next.disabled = !geser || track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
	}

	// "Baca Selengkapnya": tombol hanya tampil bila isi benar-benar terpotong.
	Array.prototype.slice.call(root.querySelectorAll('[data-tes-isi]')).forEach(function (p) {
		var btn = p.parentNode.querySelector('[data-tes-baca]');
		if (!btn) { return; }
		function ukur() {
			if (p.classList.contains('au-tes-card__isi--buka')) { return; }
			btn.hidden = p.scrollHeight <= p.clientHeight + 1;
		}
		btn.addEventListener('click', function () {
			var buka = !p.classList.contains('au-tes-card__isi--buka');
			p.classList.toggle('au-tes-card__isi--buka', buka);
			btn.textContent = buka ? 'Tutup' : 'Baca Selengkapnya';
			btn.setAttribute('aria-expanded', buka ? 'true' : 'false');
			if (buka) { btn.hidden = false; } else { ukur(); }
			perbarui();
		});
		ukur();
		if (window.ResizeObserver) { new ResizeObserver(ukur).observe(p); }
	});

	if (!track || !nav || !prev || !next) { return; }

	prev.addEventListener('click', function () { geser(-1); });
	next.addEventListener('click', function () { geser(1); });

	function geser(arah) {
		var s = langkah() * arah;
		if (!s) { return; }
		track.scrollBy({ left: s, behavior: perilaku() });
	}

	track.addEventListener('keydown', function (e) {
		if (e.key === 'ArrowLeft') { e.preventDefault(); geser(-1); }
		else if (e.key === 'ArrowRight') { e.preventDefault(); geser(1); }
		else if (e.key === 'Home') { e.preventDefault(); track.scrollTo({ left: 0, behavior: perilaku() }); }
		else if (e.key === 'End') { e.preventDefault(); track.scrollTo({ left: track.scrollWidth, behavior: perilaku() }); }
	});

	var tunda = null;
	track.addEventListener('scroll', function () {
		window.clearTimeout(tunda);
		tunda = window.setTimeout(perbarui, 70);
	}, { passive: true });

	// Drag mouse; sentuhan memakai scroll native (swipe) bawaan browser.
	var aktif = false, menyeret = false, xAwal = 0, kiriAwal = 0, jarakAkhir = 0;
	track.addEventListener('dragstart', function (e) { e.preventDefault(); });
	track.addEventListener('pointerdown', function (e) {
		if (e.pointerType !== 'mouse' || e.button !== 0) { return; }
		aktif = true; menyeret = false; xAwal = e.clientX; kiriAwal = track.scrollLeft; jarakAkhir = 0;
	});
	window.addEventListener('pointermove', function (e) {
		if (!aktif) { return; }
		var d = e.clientX - xAwal;
		if (!menyeret && Math.abs(d) > 6) { menyeret = true; }
		if (!menyeret) { return; }
		track.scrollLeft = kiriAwal - d;
		jarakAkhir = d;
	});
	function lepas() {
		if (!aktif) { return; }
		aktif = false;
		if (menyeret) {
			var s = langkah();
			if (s) {
				track.scrollTo({ left: Math.round(track.scrollLeft / s) * s, behavior: perilaku() });
			}
			perbarui();
		}
	}
	window.addEventListener('pointerup', lepas);
	window.addEventListener('pointercancel', lepas);
	track.addEventListener('click', function (e) {
		if (menyeret || Math.abs(jarakAkhir) > 6) { e.preventDefault(); e.stopPropagation(); }
		menyeret = false; jarakAkhir = 0;
	}, true);

	if (window.ResizeObserver) { new ResizeObserver(perbarui).observe(track); }
	window.addEventListener('resize', perbarui);
	perbarui();
})();
</script>
