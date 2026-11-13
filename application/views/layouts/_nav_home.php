<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Navbar publik — LBQ Al-Utsmani (design system `au-home-`).
 *
 * Dimuat oleh layouts/header.php untuk seluruh halaman publik:
 *   - varian `home`  : beranda (anchor #program / #lokasi + scroll-spy)
 *   - varian `publik`: katalog, daftar, auth, gelombang (URL nyata, tanpa spy)
 *
 * Variabel opsional (default = perilaku beranda, jadi pemanggil lama tetap aman):
 *   $navVarian    'home' | 'publik'
 *   $navAktif     kunci menu aktif: beranda|katalog|daftar|gelombang|akun
 *   $navMenu      array(array('url','label','kunci')) — menu desktop + drawer
 *   $navBottom    array(array('url','label','kunci')) — bottom navigation (4 item)
 *   $navRoleMenu  array(array('url','label')) — menu tambahan di drawer (sudah login)
 *   $navAkunUrl   URL item "Akun" pada bottom navigation
 *   $navCtaUrl    URL tombol utama di header
 *   $navCtaLabel  label tombol utama (tanpa bagian panjang)
 *   $navCtaLong   bagian label yang disembunyikan di layar < 480px
 */
$navVarian = isset($navVarian) ? $navVarian : 'home';
$navAktif = isset($navAktif) ? $navAktif : 'beranda';
$navMenu = isset($navMenu) ? $navMenu : array(
	array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
	array('url' => site_url('katalog'), 'label' => 'Katalog Kelas', 'kunci' => 'katalog'),
	array('url' => '#cara-mendaftar', 'label' => 'Cara Mendaftar', 'kunci' => 'cara'),
);
$navAkunUrl = isset($navAkunUrl)
	? $navAkunUrl
	: (empty($user) ? site_url('auth/login') : site_url('peserta/profil'));
$navBottom = isset($navBottom) ? $navBottom : array(
	array('url' => site_url('beranda'), 'label' => 'Beranda', 'kunci' => 'beranda'),
	array('url' => '#program', 'label' => 'Program', 'kunci' => 'program'),
	array('url' => '#lokasi', 'label' => 'Cabang', 'kunci' => 'lokasi'),
	array('url' => $navAkunUrl, 'label' => 'Akun', 'kunci' => 'akun'),
);
$navRoleMenu = isset($navRoleMenu) ? $navRoleMenu : array();
$navCtaUrl = isset($navCtaUrl) ? $navCtaUrl : site_url('daftar');
$navCtaLabel = isset($navCtaLabel) ? $navCtaLabel : 'Daftar';
$navCtaLong = isset($navCtaLong) ? $navCtaLong : ' Sekarang';
$navSpy = ($navVarian === 'home');

$logoHome = logo_home();
$namaLembaga = 'LBQ Al-Utsmani';
$navLoginUrl = site_url('auth/login');
$navDaftarUrl = site_url('auth/registrasi');
?>
<header class="au-home-header">
	<div class="au-home-container au-home-header__inner">
		<a class="au-home-logo" href="<?= site_url('') ?>" aria-label="<?= html_escape($namaLembaga) ?> — Beranda">
			<?php if ($logoHome): ?>
				<img class="au-home-logo__img" src="<?= html_escape($logoHome) ?>" alt="<?= html_escape($namaLembaga) ?>"
					width="2025" height="777" decoding="async">
			<?php else: ?>
				<span class="au-home-logo__fallback">LBQ <strong>Al-Utsmani</strong></span>
			<?php endif; ?>
		</a>

		<nav class="au-home-nav" aria-label="Navigasi utama">
			<?php foreach ($navMenu as $m): ?>
				<a class="au-home-nav__link<?= $m['kunci'] === $navAktif ? ' au-home-nav__link--active' : '' ?>"
					href="<?= html_escape($m['url']) ?>"<?= $m['kunci'] === $navAktif ? ' aria-current="page"' : '' ?>><?= html_escape($m['label']) ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="au-home-header__actions">
			<?php if (empty($user)): ?>
				<a class="au-home-header__login" href="<?= $navLoginUrl ?>">Login</a>
			<?php else: ?>
				<a class="au-home-header__login" href="<?= site_url('auth/logout') ?>">Keluar</a>
			<?php endif; ?>
			<a class="au-home-button au-home-button-primary au-home-header__cta" href="<?= html_escape($navCtaUrl) ?>">
				<?= html_escape($navCtaLabel) ?><span class="au-home-header__cta-long"><?= $navCtaLong ?></span>
			</a>
			<button type="button" class="au-home-burger" id="auHomeNavOpen"
				aria-controls="auHomeNavDrawer" aria-expanded="false" aria-label="Buka menu navigasi">
				<span class="au-home-burger__bar"></span>
				<span class="au-home-burger__bar"></span>
				<span class="au-home-burger__bar"></span>
			</button>
		</div>
	</div>
</header>

<div class="au-home-drawer" id="auHomeNavDrawer" role="dialog" aria-modal="true" aria-label="Menu navigasi" hidden>
	<button type="button" class="au-home-drawer__backdrop" data-au-home-close tabindex="-1" aria-label="Tutup menu"></button>
	<aside class="au-home-drawer__panel">
		<div class="au-home-drawer__head">
			<a class="au-home-logo" href="<?= site_url('') ?>" aria-label="<?= html_escape($namaLembaga) ?> — Beranda">
				<?php if ($logoHome): ?>
					<img class="au-home-logo__img" src="<?= html_escape($logoHome) ?>" alt="<?= html_escape($namaLembaga) ?>"
						width="2025" height="777" decoding="async">
				<?php else: ?>
					<span class="au-home-logo__fallback">LBQ <strong>Al-Utsmani</strong></span>
				<?php endif; ?>
			</a>
			<button type="button" class="au-home-drawer__close" id="auHomeNavClose" data-au-home-close aria-label="Tutup menu">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
				</svg>
			</button>
		</div>

		<nav class="au-home-drawer__nav" aria-label="Menu navigasi mobile">
			<p class="au-home-eyebrow">Menu</p>
			<?php foreach ($navMenu as $m): ?>
				<a class="au-home-drawer__link" href="<?= html_escape($m['url']) ?>"<?= $m['kunci'] === $navAktif ? ' aria-current="page"' : '' ?>>
					<?= html_escape($m['label']) ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
				</a>
			<?php endforeach; ?>
			<?php if ($navVarian === 'publik'): ?>
				<a class="au-home-drawer__link" href="<?= site_url('gelombang') ?>"<?= $navAktif === 'gelombang' ? ' aria-current="page"' : '' ?>>
					Pendaftaran Gelombang
					<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
				</a>
			<?php endif; ?>

			<?php if (! empty($navRoleMenu)): ?>
				<p class="au-home-eyebrow au-home-drawer__group">Menu saya</p>
				<?php foreach ($navRoleMenu as $m): ?>
					<a class="au-home-drawer__link" href="<?= html_escape($m['url']) ?>">
						<?= html_escape($m['label']) ?>
						<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</nav>

		<div class="au-home-drawer__foot">
			<?php if (empty($user)): ?>
				<div class="au-home-drawer__actions">
					<a class="au-home-button au-home-button-secondary au-home-button--block au-home-button--sm" href="<?= $navLoginUrl ?>">Login</a>
					<a class="au-home-button au-home-button-primary au-home-button--block au-home-button--sm" href="<?= $navDaftarUrl ?>">Registrasi</a>
				</div>
				<p class="au-home-drawer__note">Belum punya akun? Registrasi gratis untuk peserta maupun wali.</p>
			<?php else: ?>
				<div class="au-home-drawer__user">
					<span class="au-home-drawer__avatar"><?= html_escape(singkatan($user['nama'])) ?></span>
					<span class="au-home-drawer__userinfo">
						<span class="au-home-drawer__name"><?= html_escape($user['nama']) ?></span>
						<span class="au-home-drawer__username"><?= html_escape($user['username']) ?></span>
					</span>
				</div>
				<a class="au-home-button au-home-button-secondary au-home-button--block au-home-button--sm" href="<?= site_url('auth/logout') ?>">Keluar</a>
			<?php endif; ?>
		</div>
	</aside>
</div>

<?php
// ---------------------------------------------------------------------------
// Floating bottom navigation.
// Varian home: status aktif mengikuti section yang dibaca (scroll-spy).
// Varian publik: status aktif sudah ditentukan markup ($navAktif).
// ---------------------------------------------------------------------------
?>
<nav class="au-bottomnav" aria-label="Navigasi cepat"<?= $navSpy ? ' data-au-spy="1"' : '' ?>>
	<?php foreach ($navBottom as $b): ?>
		<a class="au-bottomnav__item" href="<?= html_escape($b['url']) ?>" data-au-bottom="<?= html_escape($b['kunci']) ?>"
			<?= $b['kunci'] === $navAktif ? 'aria-current="page"' : '' ?>>
			<span class="au-bottomnav__icon" aria-hidden="true">
				<?php if ($b['kunci'] === 'beranda'): ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
				<?php elseif ($b['kunci'] === 'katalog' || $b['kunci'] === 'program'): ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
				<?php elseif ($b['kunci'] === 'lokasi'): ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
				<?php elseif ($b['kunci'] === 'daftar'): ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"/></svg>
				<?php else: ?>
					<svg fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
				<?php endif; ?>
			</span>
			<span class="au-bottomnav__label"><?= html_escape($b['label']) ?></span>
		</a>
	<?php endforeach; ?>
</nav>

<script>
(function () {
	var drawer = document.getElementById('auHomeNavDrawer');
	var tombol = document.getElementById('auHomeNavOpen');
	if (! drawer || ! tombol) { return; }

	var tutupBtn = document.getElementById('auHomeNavClose');
	var fokusTerakhir = null;
	var header = document.querySelector('.au-home-header');

	function tampilkanNavbar() {
		if (header) { header.classList.remove('au-home-header--hidden'); }
	}

	function sembunyikanNavbar() {
		if (header && drawer.hidden) { header.classList.add('au-home-header--hidden'); }
	}

	function setBuka(buka) {
		drawer.hidden = ! buka;
		tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
		document.body.style.overflow = buka ? 'hidden' : '';

		if (buka) {
			// Navbar tidak boleh ikut tersembunyi selama drawer terbuka.
			tampilkanNavbar();
			fokusTerakhir = document.activeElement;
			if (tutupBtn) { tutupBtn.focus(); }
		} else if (fokusTerakhir && fokusTerakhir.focus) {
			fokusTerakhir.focus();
		}
	}

	tombol.addEventListener('click', function () { setBuka(true); });

	Array.prototype.forEach.call(drawer.querySelectorAll('[data-au-home-close]'), function (el) {
		el.addEventListener('click', function () { setBuka(false); });
	});

	// Menu ditutup setelah salah satu tautan dipilih (navigasi tetap berjalan).
	Array.prototype.forEach.call(drawer.querySelectorAll('a'), function (a) {
		a.addEventListener('click', function () { setBuka(false); });
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && ! drawer.hidden) { setBuka(false); }
	});

	// Tahan fokus keyboard di dalam drawer selama terbuka.
	drawer.addEventListener('keydown', function (e) {
		if (e.key !== 'Tab' || drawer.hidden) { return; }
		var fokusable = drawer.querySelectorAll('a[href], button:not([disabled]):not([tabindex="-1"])');
		if (! fokusable.length) { return; }
		var pertama = fokusable[0];
		var terakhir = fokusable[fokusable.length - 1];
		if (e.shiftKey && document.activeElement === pertama) {
			e.preventDefault();
			terakhir.focus();
		} else if (! e.shiftKey && document.activeElement === terakhir) {
			e.preventDefault();
			pertama.focus();
		}
	});

	window.addEventListener('resize', function () {
		if (window.innerWidth >= 1024 && ! drawer.hidden) { setBuka(false); }
	});

	// Smart auto-hide navbar (hanya navbar publik ini).
	// - tampil di posisi paling atas;
	// - sembunyi saat scroll ke bawah melewati ambang;
	// - muncul saat scroll ke atas;
	// - tetap tersembunyi saat scroll berhenti (tidak ada timer yang menampilkan);
	// - drawer terbuka → navbar selalu tampil.
	if (header) {
		var ambang = 8;
		var tinggiNavbar = header.offsetHeight || 80;
		var terakhirY = window.pageYOffset || 0;
		var antre = false;
		// Cursor proximity (desktop): kursor mendekati tepi atas → navbar tampil.
		var dekatAtas = false;
		var bisaHover = window.matchMedia('(hover: hover) and (pointer: fine)');

		var perbaruiNavbar = function () {
			var y = window.pageYOffset || 0;
			var selisih = y - terakhirY;

			header.classList.toggle('au-home-header--scrolled', y > 8);

			if (! drawer.hidden || dekatAtas || y <= tinggiNavbar) {
				tampilkanNavbar();
			} else if (selisih > ambang) {
				sembunyikanNavbar();
			} else if (selisih < -ambang) {
				tampilkanNavbar();
			}

			terakhirY = y;
			antre = false;
		};

		window.addEventListener('scroll', function () {
			if (antre) { return; }
			antre = true;
			window.requestAnimationFrame(perbaruiNavbar);
		}, { passive: true });

		// Kursor masuk zona atas (±tinggi navbar + 24px) → tampilkan navbar
		// tanpa perlu scroll. Hanya untuk perangkat ber-hover (desktop).
		var perbaruiKursor = function (y) {
			if (! bisaHover.matches || window.innerWidth < 1024) {
				dekatAtas = false;
				return;
			}

			var dekat = y <= tinggiNavbar + 24;
			if (dekat === dekatAtas) { return; }
			dekatAtas = dekat;

			if (dekat && (window.pageYOffset || 0) > tinggiNavbar) {
				tampilkanNavbar();
				// Cegah delta scroll berikutnya langsung menyembunyikan lagi.
				terakhirY = window.pageYOffset || 0;
			}
		};

		document.addEventListener('mousemove', function (e) {
			perbaruiKursor(e.clientY);
		}, { passive: true });

		// Jaga akurasi zona kursor saat viewport berubah (mobile ↔ desktop).
		window.addEventListener('resize', function () {
			tinggiNavbar = header.offsetHeight || 80;
		});

		// Pastikan navbar terlihat saat halaman dimuat / di-refresh.
		tampilkanNavbar();
	}
})();
</script>

<?php if ($navSpy): ?>
<script>
// Scroll-spy bottom navigation (khusus beranda). Status aktif mengikuti section
// yang sedang dibaca. Tanpa JS, "Beranda" tetap aktif dari markup.
(function () {
	var nav = document.querySelector('.au-bottomnav[data-au-spy="1"]');
	if (! nav) { return; }

	var tautan = Array.prototype.slice.call(nav.querySelectorAll('[data-au-bottom]'));
	if (! tautan.length) { return; }

	// Section target baru ada setelah <main> dirender (script ini berada di
	// header), jadi elemen dicari saat dibutuhkan — bukan saat parse.
	function bagianAtas(idBagian) {
		var el = document.getElementById(idBagian);
		if (! el) { return null; }
		return el.getBoundingClientRect().top + (window.pageYOffset || 0);
	}

	function setAktif(nama) {
		tautan.forEach(function (a) {
			if (a.getAttribute('data-au-bottom') === nama) {
				a.setAttribute('aria-current', 'page');
			} else {
				a.removeAttribute('aria-current');
			}
		});
	}

	var antre = false;
	function perbarui() {
		antre = false;
		// Titik baca: 42% tinggi viewport — section dianggap aktif ketika
		// bagian atasnya sudah melewati titik ini.
		var titik = (window.pageYOffset || 0) + window.innerHeight * 0.42;
		var atasProgram = bagianAtas('program');
		var atasLokasi = bagianAtas('lokasi');
		var nama = 'beranda';

		if (atasProgram !== null && titik >= atasProgram) { nama = 'program'; }
		if (atasLokasi !== null && titik >= atasLokasi) { nama = 'lokasi'; }

		setAktif(nama);
	}

	function minta() {
		if (antre) { return; }
		antre = true;
		window.requestAnimationFrame(perbarui);
	}

	// Klik anchor → tandai aktif langsung (scroll halus ditangani CSS).
	tautan.forEach(function (a) {
		a.addEventListener('click', function () {
			var nama = a.getAttribute('data-au-bottom');
			if (nama === 'program' || nama === 'lokasi') { setAktif(nama); }
		});
	});

	window.addEventListener('scroll', minta, { passive: true });
	window.addEventListener('resize', minta);

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', perbarui);
	} else {
		perbarui();
	}
})();
</script>
<?php endif; ?>
