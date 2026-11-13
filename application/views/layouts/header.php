<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Layout header aplikasi.
 *
 * - Admin        : sidebar melayang (desktop) + drawer (mobile) + top bar.
 * - Publik       : navbar design system `au-home-` (beranda, katalog, daftar,
 *                  auth, gelombang) + drawer + bottom navigation.
 * - Guru/peserta : floating pill nav; di < lg tautannya pindah ke drawer.
 *
 * Token desain (warna/radius/shadow/font) mengikuti `design-system.md` (tema Donezo)
 * dan didefinisikan sekali di sini lewat `tailwind.config` + kelas komponen.
 * Kelas komponen yang tersedia: `card`, `card-head`, `card-body`, `btn`, `btn-primary`,
 * `btn-outline`, `btn-ghost`, `btn-danger`, `btn-sm`, `btn-icon`, `input`, `label`,
 * `badge`, `th`, `td`, `nav-item`, `nav-item-active`, `stat-card`, `page-title`.
 */
$role = ! empty($user) ? $user['role'] : NULL;
// Layout admin (sidebar melayang) hanya untuk area /admin/... — admin yang
// membuka halaman publik (beranda, katalog, dst.) tetap memakai nav publik.
$seg1 = $this->uri->segment(1);
$isiAdmin = ($role === 'admin' && $seg1 === 'admin');
$seg2 = $this->uri->segment(2);
// Halaman publik memakai navbar design system `au-home-` (sama dengan beranda),
// baik untuk tamu maupun pengguna yang sudah login. Area admin/guru/peserta
// tetap memakai layout lamanya masing-masing.
$isiPublik = in_array($seg1, array(NULL, '', 'beranda', 'katalog', 'daftar', 'auth', 'gelombang'), TRUE);
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= html_escape(! empty($title) ? $title.' — LBQ Al-Utsmani' : 'LBQ Al-Utsmani') ?></title>
	<?php $logo = logo(); ?>
	<?php if ($logo): ?><link rel="icon" href="<?= html_escape($logo) ?>"><?php endif; ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
	tailwind.config = {
		theme: {
			extend: {
				colors: {
					primary: { DEFAULT: '#8B5E34', light: '#C89B6A', soft: '#F7EFE3', dark: '#6B4423', deep: '#3F2915' },
					canvas: '#FAF6F0',
					ink: '#1F1A16',
					muted: '#7A6A5A',
					line: '#EDE3D7',
					// Peta ulang emerald/teal ke palet Caramel/Gold-brown agar seluruh
					// kelas lama (bg-emerald-600, dst.) otomatis konsisten brand.
					emerald: { 50: '#FBF7F0', 100: '#F7EFE3', 200: '#EBDCC6', 300: '#DBC3A2', 400: '#C89B6A', 500: '#B0813F', 600: '#8B5E34', 700: '#6B4423', 800: '#513218', 900: '#3F2915' },
					teal: { 50: '#FBF7F0', 100: '#F5EBDD', 200: '#E8D6BE', 300: '#D6B893', 400: '#C0955F', 500: '#A87A44', 600: '#8B5E34', 700: '#6B4423', 800: '#513218', 900: '#3F2915' },
					// Neutral dibiarkan netral (gray) agar teks & border tetap kontras.
					slate: { 50: '#F9FAFB', 100: '#F3F4F6', 200: '#E5E7EB', 300: '#D1D5DB', 400: '#9CA3AF', 500: '#6B7280', 600: '#4B5563', 700: '#374151', 800: '#1F2937', 900: '#111827' },
				},
				fontFamily: { sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'] },
				borderRadius: { lg: '1rem', xl: '0.75rem', '2xl': '1.25rem' },
				boxShadow: {
					card: '0 4px 12px rgba(0, 0, 0, 0.04)',
					float: '0 12px 32px rgba(0, 0, 0, 0.08)',
					glow: '0 8px 20px rgba(139, 94, 52, 0.25)',
				},
			},
		},
	};
	</script>
	<style type="text/tailwindcss">
		@layer components {
			.card { @apply rounded-lg border border-line bg-white shadow-card; }
			.card-head { @apply flex items-center justify-between gap-3 border-b border-line px-5 py-4; }
			.card-body { @apply p-5; }
			.page-title { @apply text-xl font-bold text-ink sm:text-2xl; }
			.btn { @apply inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition-colors; }
			.btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-dark; }
			.btn-outline { @apply inline-flex items-center justify-center gap-2 rounded-full border border-primary px-5 py-2.5 text-sm font-semibold text-primary transition-colors hover:bg-primary-soft; }
			.btn-ghost { @apply inline-flex items-center justify-center gap-2 rounded-full border border-line bg-white px-5 py-2.5 text-sm font-semibold text-ink transition-colors hover:bg-canvas; }
			.btn-danger { @apply inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-rose-700; }
			.btn-sm { @apply px-3.5 py-1.5 text-xs; }
			.btn-icon { @apply inline-flex h-9 w-9 items-center justify-center rounded-full border border-line bg-white text-muted transition-colors hover:bg-canvas hover:text-ink; }
			.input { @apply block w-full rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/25; }
			.label { @apply mb-1.5 block text-xs font-semibold text-muted; }
			.error { @apply mt-1.5 block text-xs font-medium text-rose-600; }
			.badge { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold; }
			.stat-card { @apply rounded-lg border border-line bg-white p-5 shadow-card; }
			.th { @apply whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted; }
			.td { @apply px-4 py-3 text-sm text-ink; }
			.nav-item { @apply flex items-center gap-3 rounded-full px-4 py-2.5 text-sm font-medium text-muted transition-colors hover:bg-primary-soft hover:text-primary; }
			.nav-item-active { @apply flex items-center gap-3 rounded-full bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-glow; }
			/* Nav publik di atas hero (beranda): kaca transparan seperti kartu
			   "Program yang Berjalan". Teks dibuat terang; saat halaman di-scroll,
			   JS mengubah `data-solid` ke "1" agar pill jadi pekat + teks gelap
			   supaya tetap terbaca di atas konten terang. */
			.nav-kaca { @apply rounded-full border border-white/20 bg-white/10 backdrop-blur-md transition-colors; box-shadow: 0 10px 40px -12px rgba(31, 26, 22, 0.35); }
			.nav-kaca[data-solid="1"] { @apply border-line bg-canvas/85 shadow-card backdrop-blur-xl; }
			.nav-kaca-teks { @apply text-white/85 transition-colors hover:bg-white/15 hover:text-white; }
			.nav-kaca-aktif { @apply text-primary-light hover:bg-white/15 hover:text-white; }
			.nav-kaca-keluar { @apply text-rose-200 hover:bg-white/10 hover:text-white; }
			.nav-kaca-nama { @apply text-white/75; }
			.nav-kaca-sep { @apply text-white/30; }
			.nav-kaca[data-solid="1"] .nav-kaca-teks { @apply text-slate-700 hover:bg-white/70 hover:text-primary; }
			.nav-kaca[data-solid="1"] .nav-kaca-aktif { @apply text-primary hover:bg-white/70; }
			.nav-kaca[data-solid="1"] .nav-kaca-keluar { @apply text-rose-600 hover:bg-rose-500/10; }
			.nav-kaca[data-solid="1"] .nav-kaca-nama { @apply text-slate-500; }
			.nav-kaca[data-solid="1"] .nav-kaca-sep { @apply text-slate-300; }
		}
	</style>
	<?php // Design system homepage (namespace `au-home-`). Dimuat setelah Tailwind CDN & layer komponen di atas agar class khusus tidak tertimpa. ?>
	<?php
	// Cache-busting: Apache tidak mengirim Cache-Control untuk aset statis, jadi
	// browser bisa memakai CSS lama. Versi = mtime file → URL berubah tiap edit.
	$auHomeCss = FCPATH . 'assets/css/alutsmani-home.css';
	$auHomeCssVer = is_file($auHomeCss) ? filemtime($auHomeCss) : '';
	?>
	<link rel="stylesheet" href="<?= base_url('assets/css/alutsmani-home.css') ?><?= $auHomeCssVer ? '?v='.$auHomeCssVer : '' ?>">
</head>
<body class="min-h-screen bg-canvas font-sans text-ink">

<?php if ($isiAdmin): ?>
	<?php
	// ---- Admin: sidebar melayang (desktop) ----
	$navData = array('seg2' => $seg2, 'user' => $user);
	?>
	<aside class="fixed bottom-4 left-4 top-4 z-40 hidden w-64 flex-col rounded-2xl border border-line bg-white p-4 shadow-float lg:flex">
		<a href="<?= site_url('admin/dashboard') ?>" class="block px-2 py-1">
			<?php if ($logo): ?>
				<img src="<?= html_escape($logo) ?>" alt="LBQ Al-Utsmani" class="h-9 w-auto">
			<?php else: ?>
				<span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-black text-white">AU</span>
			<?php endif; ?>
			<span class="mt-1 block text-[11px] font-medium text-muted">Panel Admin</span>
		</a>
		<nav class="mt-6 flex-1 space-y-1 overflow-y-auto">
			<?php $this->load->view('layouts/_nav_admin', $navData); ?>
		</nav>
		<div class="mt-4 border-t border-line pt-4">
			<div class="flex items-center gap-3 px-1">
				<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary"><?= html_escape(singkatan($user['nama'])) ?></span>
				<span class="min-w-0 flex-1">
					<span class="block truncate text-sm font-semibold text-ink"><?= html_escape($user['nama']) ?></span>
					<span class="block truncate text-[11px] text-muted"><?= html_escape($user['username']) ?></span>
				</span>
				<a href="<?= site_url('auth/logout') ?>" class="btn-icon" title="Keluar" aria-label="Keluar">
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
				</a>
			</div>
		</div>
	</aside>

	<!-- Admin: drawer mobile -->
	<div id="adminDrawer" class="fixed inset-0 z-50 hidden lg:hidden" role="dialog" aria-modal="true">
		<button type="button" data-drawer-close class="absolute inset-0 h-full w-full bg-black/40 backdrop-blur-sm" aria-label="Tutup menu"></button>
		<aside class="absolute bottom-0 left-0 top-0 w-72 max-w-[85%] overflow-y-auto rounded-r-2xl bg-white p-4 shadow-float">
			<div class="flex items-center justify-between">
				<a href="<?= site_url('admin/dashboard') ?>" class="flex items-center gap-2.5">
					<?php if ($logo): ?>
						<img src="<?= html_escape($logo) ?>" alt="LBQ Al-Utsmani" class="h-9 w-auto">
					<?php else: ?>
						<span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-black text-white">AU</span>
						<span class="text-sm font-bold text-ink">LBQ Al-Utsmani</span>
					<?php endif; ?>
				</a>
				<button type="button" data-drawer-close class="btn-icon" aria-label="Tutup">
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</div>
			<nav class="mt-5 space-y-1">
				<?php $this->load->view('layouts/_nav_admin', $navData); ?>
			</nav>
			<div class="mt-5 border-t border-line pt-4">
				<div class="flex items-center gap-3">
					<span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary"><?= html_escape(singkatan($user['nama'])) ?></span>
					<span class="min-w-0 flex-1">
						<span class="block truncate text-sm font-semibold text-ink"><?= html_escape($user['nama']) ?></span>
						<span class="block truncate text-[11px] text-muted"><?= html_escape($user['username']) ?></span>
					</span>
				</div>
				<a href="<?= site_url('auth/logout') ?>" class="btn-ghost btn-sm mt-3 w-full">Keluar</a>
			</div>
		</aside>
	</div>

	<div class="lg:pl-72">
		<header class="sticky top-0 z-30 border-b border-line bg-canvas/80 backdrop-blur">
			<div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
				<button type="button" id="drawerOpen" class="btn-icon lg:hidden" aria-label="Buka menu">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
				</button>
				<a href="<?= site_url('admin/dashboard') ?>" class="lg:hidden">
					<?php if ($logo): ?>
						<img src="<?= html_escape($logo) ?>" alt="LBQ Al-Utsmani" class="h-8 w-auto">
					<?php else: ?>
						<span class="font-bold text-primary">LBQ Al-Utsmani</span>
					<?php endif; ?>
				</a>
				<form action="<?= site_url('admin/peserta') ?>" method="get" class="hidden md:block">
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
							<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
						</span>
						<input type="search" name="q" class="input w-64 rounded-full pl-10" placeholder="Cari peserta…">
					</div>
				</form>
				<div class="ml-auto flex items-center gap-3">
					<span class="hidden text-sm text-muted sm:block"><?= html_escape($user['nama']) ?></span>
					<a href="<?= site_url('auth/logout') ?>" class="btn-icon sm:hidden" title="Keluar" aria-label="Keluar">
						<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
					</a>
				</div>
			</div>
		</header>
		<main class="px-4 py-6 sm:px-6 lg:px-8">

<?php elseif ($isiPublik): ?>
	<?php
	// Halaman publik (beranda, katalog, daftar, auth, gelombang): navbar homepage
	// baru. Varian `home` mempertahankan anchor beranda (#cara-mendaftar,
	// #program, #lokasi); varian `publik` memakai URL halaman sebenarnya.
	$navVarian = ! empty($main_penuh) ? 'home' : 'publik';
	$navAktif = 'beranda';
	if ($seg1 === 'katalog') {
		$navAktif = 'katalog';
	} elseif ($seg1 === 'daftar') {
		$navAktif = 'daftar';
	} elseif ($seg1 === 'gelombang') {
		$navAktif = 'gelombang';
	} elseif ($seg1 === 'auth') {
		$navAktif = 'akun';
	}

	$navMenu = ($navVarian === 'home')
		? array(
			array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
			array('url' => site_url('katalog'), 'label' => 'Katalog Kelas', 'kunci' => 'katalog'),
			array('url' => '#cara-mendaftar', 'label' => 'Cara Mendaftar', 'kunci' => 'cara'),
		)
		: array(
			array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
			array('url' => site_url('katalog'), 'label' => 'Katalog Kelas', 'kunci' => 'katalog'),
			array('url' => site_url('daftar'), 'label' => 'Daftar Kelas', 'kunci' => 'daftar'),
		);

	// Aksi header + menu drawer mengikuti status login.
	$navAkunUrl = site_url('auth/login');
	$navCtaUrl = site_url('daftar');
	$navCtaLabel = 'Daftar';
	$navCtaLong = ' Sekarang';
	$navRoleMenu = array();

	if (! empty($user)) {
		if ($user['role'] === 'admin') {
			$navAkunUrl = site_url('admin/dashboard');
			$navCtaUrl = site_url('admin/dashboard');
			$navCtaLabel = 'Panel';
			$navCtaLong = ' Admin';
			$navRoleMenu = array(
				array('url' => site_url('admin/dashboard'), 'label' => 'Panel Admin'),
				array('url' => site_url('admin/peserta'), 'label' => 'Data Peserta'),
				array('url' => site_url('admin/pembayaran'), 'label' => 'Pembayaran'),
			);
		} elseif ($user['role'] === 'guru') {
			$navAkunUrl = site_url('guru/dashboard');
			$navCtaUrl = site_url('guru/dashboard');
			$navCtaLabel = 'Dashboard';
			$navCtaLong = '';
			$navRoleMenu = array(
				array('url' => site_url('guru/dashboard'), 'label' => 'Dashboard Guru'),
				array('url' => site_url('guru/kelas'), 'label' => 'Kelas Saya'),
			);
		} else {
			$navAkunUrl = site_url('peserta/profil');
			$navCtaUrl = site_url('peserta/dashboard');
			$navCtaLabel = 'Dashboard';
			$navCtaLong = '';
			$navRoleMenu = array(
				array('url' => site_url('peserta/dashboard'), 'label' => 'Dashboard'),
				array('url' => site_url('peserta/pendaftaran'), 'label' => 'Pendaftaran'),
				array('url' => site_url('peserta/riwayat/absensi'), 'label' => 'Riwayat Absensi'),
				array('url' => site_url('peserta/riwayat/progres'), 'label' => 'Progres'),
				array('url' => site_url('peserta/pembayaran'), 'label' => 'Pembayaran'),
				array('url' => site_url('peserta/profil'), 'label' => 'Profil'),
			);
		}
	}

	$navBottom = ($navVarian === 'home')
		? array(
			array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
			array('url' => '#program', 'label' => 'Program', 'kunci' => 'program'),
			array('url' => '#lokasi', 'label' => 'Cabang', 'kunci' => 'lokasi'),
			array('url' => $navAkunUrl, 'label' => 'Akun', 'kunci' => 'akun'),
		)
		: array(
			array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
			array('url' => site_url('katalog'), 'label' => 'Katalog', 'kunci' => 'katalog'),
			array('url' => site_url('daftar'), 'label' => 'Daftar', 'kunci' => 'daftar'),
			array('url' => $navAkunUrl, 'label' => 'Akun', 'kunci' => 'akun'),
		);

	$this->load->view('layouts/_nav_home', array(
		'navVarian' => $navVarian,
		'navAktif' => $navAktif,
		'navMenu' => $navMenu,
		'navBottom' => $navBottom,
		'navRoleMenu' => $navRoleMenu,
		'navAkunUrl' => $navAkunUrl,
		'navCtaUrl' => $navCtaUrl,
		'navCtaLabel' => $navCtaLabel,
		'navCtaLong' => $navCtaLong,
		'user' => isset($user) ? $user : NULL,
	));
	?>
	<?php // $main_penuh = konten mengatur wadahnya sendiri (mis. hero full-bleed di beranda). ?>
	<main class="<?= ! empty($main_penuh) ? 'w-full' : 'au-publik' ?>">

<?php else: ?>
	<?php
	// Nav publik: di beranda (`main_penuh`, hero full-bleed) pill memakai kaca
	// transparan agar gambar hero tembus — sama seperti kartu "Program yang
	// Berjalan". Teks jadi terang; JS di bawah mengubah `data-solid` saat halaman
	// di-scroll supaya pill pekat lagi dan teks tetap terbaca. Halaman publik lain
	// (katalog, login, dst.) tetap memakai gaya pill lama.
	$diHero = ! empty($main_penuh);
	$kelasPill = $diHero
		? 'nav-kaca'
		: 'rounded-full border border-white/50 bg-white/40 bg-gradient-to-b from-white/55 to-white/30 shadow-[0_10px_40px_-12px_rgba(31,26,22,0.35)] ring-1 ring-white/40 backdrop-blur-2xl backdrop-saturate-150';
	$kelasLink = $diHero ? 'nav-kaca-teks' : 'text-slate-700 hover:bg-white/70 hover:text-primary';
	$kelasLinkAktif = $diHero ? 'nav-kaca-aktif font-semibold' : 'font-semibold text-primary hover:bg-white/70';
	$kelasSep = $diHero ? 'nav-kaca-sep' : 'text-slate-300';
	$kelasNama = $diHero ? 'nav-kaca-nama' : 'text-slate-500';
	$kelasKeluar = $diHero ? 'nav-kaca-keluar font-medium' : 'font-medium text-rose-600 hover:bg-rose-500/10';
	?>
	<?php
	// Menu utama per role: satu sumber data, dirender dua kali — pill (>= lg) dan
	// drawer (< lg). `cta` = tautan utama yang selalu disorot (coklat).
	if (empty($user)) {
		$menuUtama = array(
			array('url' => 'beranda', 'label' => 'Beranda', 'cta' => FALSE),
			array('url' => 'katalog', 'label' => 'Katalog Kelas', 'cta' => FALSE),
		);
	} elseif ($user['role'] === 'guru') {
		$menuUtama = array(
			array('url' => 'guru/dashboard', 'label' => 'Dashboard', 'cta' => FALSE),
			array('url' => 'guru/kelas', 'label' => 'Kelas Saya', 'cta' => FALSE),
		);
	} elseif ($user['role'] === 'admin') {
		$menuUtama = array(
			array('url' => 'admin/dashboard', 'label' => 'Panel Admin', 'cta' => TRUE),
			array('url' => 'katalog', 'label' => 'Katalog', 'cta' => FALSE),
		);
	} else {
		$menuUtama = array(
			array('url' => 'peserta/dashboard', 'label' => 'Dashboard', 'cta' => FALSE),
			array('url' => 'daftar', 'label' => 'Daftar Kelas', 'cta' => TRUE),
			array('url' => 'peserta/pendaftaran', 'label' => 'Pendaftaran', 'cta' => FALSE),
			array('url' => 'peserta/riwayat/absensi', 'label' => 'Riwayat Absensi', 'cta' => FALSE),
			array('url' => 'peserta/riwayat/progres', 'label' => 'Progres', 'cta' => FALSE),
			array('url' => 'peserta/pembayaran', 'label' => 'Pembayaran', 'cta' => FALSE),
			array('url' => 'peserta/profil', 'label' => 'Profil', 'cta' => FALSE),
		);
	}
	?>
	<header class="sticky top-0 z-40 px-3 pt-3 sm:px-4">
		<?php // >= lg: pill penuh (tamu: 3 kolom); < lg: logo + Registrasi (tamu) + hamburger, tautan pindah ke drawer. ?>
		<div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-2 <?= $kelasPill ?> <?= empty($user) ? 'lg:grid lg:grid-cols-[1fr_auto_1fr]' : '' ?>"<?= $diHero ? ' id="navKaca" data-solid="0"' : '' ?>>
			<a href="<?= site_url('') ?>" class="flex items-center gap-2 font-bold text-primary <?= empty($user) ? 'lg:justify-self-start' : '' ?>">
				<?php if ($logo): ?>
					<?php // Logo berupa JPG berlatar putih → dibungkus chip putih agar tidak tampak kotak di atas navbar kaca. ?>
					<span class="rounded-lg bg-white/95 px-1.5 py-0.5 shadow-sm ring-1 ring-white/60">
						<img src="<?= html_escape($logo) ?>" alt="LBQ Al-Utsmani" class="block h-7 w-auto sm:h-8">
					</span>
				<?php else: ?>
					<span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-black text-white">AU</span>
					<span class="text-sm sm:text-base">LBQ Al-Utsmani</span>
				<?php endif; ?>
			</a>

			<nav class="hidden items-center gap-1 text-sm lg:flex <?= empty($user) ? 'lg:justify-self-center' : '' ?>">
				<?php foreach ($menuUtama as $m): ?>
					<a href="<?= site_url($m['url']) ?>" class="rounded-full px-3 py-1.5 <?= $m['cta'] ? $kelasLinkAktif : $kelasLink ?>"><?= html_escape($m['label']) ?></a>
				<?php endforeach; ?>
				<?php if (! empty($user)): ?>
					<span class="px-1 <?= $kelasSep ?>">|</span>
					<span class="px-1 <?= $kelasNama ?>"><?= html_escape($user['nama']) ?></span>
					<a href="<?= site_url('auth/logout') ?>" class="rounded-full px-3 py-1.5 <?= $kelasKeluar ?>">Keluar</a>
				<?php endif; ?>
			</nav>

			<?php if (empty($user)): ?>
				<div class="ml-auto flex items-center gap-2 text-sm lg:ml-0 lg:justify-self-end">
					<a href="<?= site_url('auth/login') ?>" class="hidden rounded-full px-3 py-1.5 lg:inline-block <?= $kelasLink ?>">Login</a>
					<a href="<?= site_url('auth/registrasi') ?>" class="btn-primary btn-sm">Registrasi</a>
					<button type="button" id="navDrawerOpen" class="btn-icon h-10 w-10 lg:hidden <?= $diHero ? 'border-white/30 bg-white/15 text-white hover:bg-white/25 hover:text-white' : '' ?>"
						aria-controls="navDrawer" aria-expanded="false" aria-label="Buka menu">
						<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
					</button>
				</div>

			<?php else: ?>
				<button type="button" id="navDrawerOpen" class="btn-icon ml-auto h-10 w-10 lg:hidden <?= $diHero ? 'border-white/30 bg-white/15 text-white hover:bg-white/25 hover:text-white' : '' ?>"
					aria-controls="navDrawer" aria-expanded="false" aria-label="Buka menu">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
				</button>
			<?php endif; ?>
		</div>
	</header>

	<?php // Drawer menu publik (< lg): pola sama dengan drawer admin. ?>
	<div id="navDrawer" class="fixed inset-0 z-50 hidden lg:hidden" role="dialog" aria-modal="true" aria-label="Menu navigasi">
		<button type="button" data-nav-close class="absolute inset-0 h-full w-full bg-black/40 backdrop-blur-sm" aria-label="Tutup menu"></button>
		<aside class="absolute bottom-0 right-0 top-0 flex w-72 max-w-[85%] flex-col overflow-y-auto rounded-l-2xl bg-white p-5 shadow-float">
			<div class="flex items-center justify-between">
				<a href="<?= site_url('') ?>" class="flex items-center gap-2 font-bold text-primary">
					<?php if ($logo): ?>
						<span class="rounded-lg bg-white px-1.5 py-0.5 shadow-sm ring-1 ring-line">
							<img src="<?= html_escape($logo) ?>" alt="LBQ Al-Utsmani" class="block h-7 w-auto">
						</span>
					<?php else: ?>
						<span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-black text-white">AU</span>
						<span class="text-sm">LBQ Al-Utsmani</span>
					<?php endif; ?>
				</a>
				<button type="button" data-nav-close class="btn-icon" aria-label="Tutup">
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</div>

			<nav class="mt-5 space-y-1" aria-label="Menu utama">
				<?php foreach ($menuUtama as $m): ?>
					<a href="<?= site_url($m['url']) ?>" class="<?= $m['cta'] ? 'nav-item-active' : 'nav-item' ?> py-3"><?= html_escape($m['label']) ?></a>
				<?php endforeach; ?>
			</nav>

			<div class="mt-auto border-t border-line pt-4">
				<?php if (empty($user)): ?>
					<a href="<?= site_url('auth/login') ?>" class="btn-ghost btn-sm w-full">Login</a>
					<a href="<?= site_url('auth/registrasi') ?>" class="btn-primary btn-sm mt-2 w-full">Registrasi</a>
				<?php else: ?>
					<div class="flex items-center gap-3">
						<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary"><?= html_escape(singkatan($user['nama'])) ?></span>
						<span class="min-w-0 flex-1">
							<span class="block truncate text-sm font-semibold text-ink"><?= html_escape($user['nama']) ?></span>
							<span class="block truncate text-[11px] text-muted"><?= html_escape($user['username']) ?></span>
						</span>
					</div>
					<a href="<?= site_url('auth/logout') ?>" class="btn-ghost btn-sm mt-3 w-full">Keluar</a>
				<?php endif; ?>
			</div>
		</aside>
	</div>
	<?php if ($diHero): ?>
	<?php // Kaca transparan hanya di puncak beranda; setelah di-scroll pill dibuat pekat. ?>
	<script>
	(function () {
		var pill = document.getElementById('navKaca');
		if (! pill) { return; }
		var heroBg = null;

		// Kaca hanya selama gambar hero benar-benar berada di belakang pill; kalau
		// ada flash message yang mendorongnya turun, pill langsung dibuat pekat.
		function setSolid() {
			if (! heroBg) {
				// Lapisan gambar hero (`#hero-slider`) ditarik ke atas menimpa area
				// pill, jadi acuannya elemen itu; kalau tidak ada, pakai section hero.
				heroBg = document.getElementById('hero-slider') || document.querySelector('main > section');
			}
			var pekat = window.scrollY > 16;
			if (! pekat && heroBg) {
				pekat = heroBg.getBoundingClientRect().top > pill.getBoundingClientRect().bottom;
			}
			pill.setAttribute('data-solid', pekat ? '1' : '0');
		}

		function mulai() {
			setSolid();
			window.addEventListener('scroll', setSolid, { passive: true });
			window.addEventListener('resize', setSolid);
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', mulai);
		} else {
			mulai();
		}
	})();
	</script>
	<?php endif; ?>
	<?php // $main_penuh = konten mengatur wadahnya sendiri (mis. hero full-bleed di beranda). ?>
	<main class="<?= ! empty($main_penuh) ? 'w-full' : 'mx-auto max-w-6xl px-4 py-6' ?>">

<?php endif; ?>

	<?php if (! empty($flash_sukses) || ! empty($flash_gagal)): ?>
	<?php
	$kelasFlash = '';
	if (! empty($main_penuh)) {
		$kelasFlash = 'relative z-50 mx-auto max-w-6xl px-4 pt-6';
	} elseif ($isiPublik) {
		// Halaman publik memakai wadah 1440px yang sama dengan kontennya.
		$kelasFlash = 'au-home-container';
	}
	?>
	<div class="<?= $kelasFlash ?>">
		<?php if (! empty($flash_sukses)): ?>
			<div class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-primary-soft px-4 py-3 text-sm text-primary-dark">
				<svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				<span><?= html_escape($flash_sukses) ?></span>
			</div>
		<?php endif; ?>
		<?php if (! empty($flash_gagal)): ?>
			<div class="mb-5 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
				<svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
				<span><?= html_escape($flash_gagal) ?></span>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
