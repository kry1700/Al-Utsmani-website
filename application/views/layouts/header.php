<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Layout header aplikasi.
 *
 * - Admin  : sidebar melayang (desktop) + drawer (mobile) + top bar.
 * - Lainnya: floating pill nav (publik/guru/peserta).
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
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
		}
	</style>
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

<?php else: ?>
	<header class="sticky top-0 z-40 px-3 pt-3 sm:px-4">
		<?php // Tamu: 3 kolom di md+ (logo kiri, Beranda+Katalog tengah, Login+Registrasi kanan). ?>
		<div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 rounded-full border border-white/50 bg-white/40 bg-gradient-to-b from-white/55 to-white/30 px-4 py-2 shadow-[0_10px_40px_-12px_rgba(31,26,22,0.35)] ring-1 ring-white/40 backdrop-blur-2xl backdrop-saturate-150 <?= empty($user) ? 'md:grid md:grid-cols-[1fr_auto_1fr]' : '' ?>">
			<a href="<?= site_url('') ?>" class="flex items-center gap-2 font-bold text-primary <?= empty($user) ? 'md:justify-self-start' : '' ?>">
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

			<?php if (empty($user)): ?>
				<nav class="flex items-center gap-1 text-sm md:justify-self-center">
					<a href="<?= site_url('beranda') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Beranda</a>
					<a href="<?= site_url('katalog') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Katalog Kelas</a>
				</nav>
				<div class="ml-auto flex items-center gap-1 text-sm md:ml-0 md:justify-self-end">
					<a href="<?= site_url('auth/login') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Login</a>
					<a href="<?= site_url('auth/registrasi') ?>" class="btn-primary btn-sm">Registrasi</a>
				</div>

			<?php else: ?>
				<nav class="flex flex-wrap items-center gap-x-1 gap-y-1 text-sm">
					<?php if ($user['role'] === 'guru'): ?>
						<a href="<?= site_url('guru/dashboard') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Dashboard</a>
						<a href="<?= site_url('guru/kelas') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Kelas Saya</a>
						<span class="px-1 text-slate-300">|</span>
						<span class="px-1 text-slate-500"><?= html_escape($user['nama']) ?></span>
						<a href="<?= site_url('auth/logout') ?>" class="rounded-full px-3 py-1.5 font-medium text-rose-600 hover:bg-rose-500/10">Keluar</a>

					<?php elseif ($user['role'] === 'admin'): ?>
						<a href="<?= site_url('admin/dashboard') ?>" class="rounded-full px-3 py-1.5 font-semibold text-primary hover:bg-white/70">Panel Admin</a>
						<a href="<?= site_url('katalog') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Katalog</a>
						<span class="px-1 text-slate-300">|</span>
						<span class="px-1 text-slate-500"><?= html_escape($user['nama']) ?></span>
						<a href="<?= site_url('auth/logout') ?>" class="rounded-full px-3 py-1.5 font-medium text-rose-600 hover:bg-rose-500/10">Keluar</a>

					<?php else: /* peserta / wali */ ?>
						<a href="<?= site_url('peserta/dashboard') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Dashboard</a>
						<a href="<?= site_url('daftar') ?>" class="rounded-full px-3 py-1.5 font-semibold text-primary hover:bg-white/70">Daftar Kelas</a>
						<a href="<?= site_url('peserta/pendaftaran') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Pendaftaran</a>
						<a href="<?= site_url('peserta/riwayat/absensi') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Riwayat Absensi</a>
						<a href="<?= site_url('peserta/riwayat/progres') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Progres</a>
						<a href="<?= site_url('peserta/pembayaran') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Pembayaran</a>
						<a href="<?= site_url('peserta/profil') ?>" class="rounded-full px-3 py-1.5 text-slate-700 hover:bg-white/70 hover:text-primary">Profil</a>
						<span class="px-1 text-slate-300">|</span>
						<span class="px-1 text-slate-500"><?= html_escape($user['nama']) ?></span>
						<a href="<?= site_url('auth/logout') ?>" class="rounded-full px-3 py-1.5 font-medium text-rose-600 hover:bg-rose-500/10">Keluar</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>
		</div>
	</header>
	<?php // $main_penuh = konten mengatur wadahnya sendiri (mis. hero full-bleed di beranda). ?>
	<main class="<?= ! empty($main_penuh) ? 'w-full' : 'mx-auto max-w-6xl px-4 py-6' ?>">

<?php endif; ?>

	<?php if (! empty($flash_sukses) || ! empty($flash_gagal)): ?>
	<div class="<?= ! empty($main_penuh) ? 'relative z-50 mx-auto max-w-6xl px-4 pt-6' : '' ?>">
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
