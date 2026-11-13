<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $isiAdmin = (! empty($user) && $user['role'] === 'admin' && $this->uri->segment(1) === 'admin'); ?>
<?php
// Footer halaman publik memakai gaya homepage (`au-home-footer` + wadah 1440px);
// footer admin/guru/peserta tetap seperti semula.
$seg1 = $this->uri->segment(1);
$isiPublik = in_array($seg1, array(NULL, '', 'beranda', 'katalog', 'daftar', 'auth', 'gelombang'), TRUE);
$kelasFooter = 'flex flex-wrap justify-between gap-2 border-t border-line py-6 text-sm text-muted ';
if ($isiAdmin) {
	$kelasFooter .= 'px-4 sm:px-6 lg:px-8';
} elseif ($isiPublik) {
	$kelasFooter = 'au-home-container au-home-footer__inner';
} else {
	$kelasFooter .= 'mx-auto max-w-6xl px-4';
}
?>
	</main>

	<footer<?= $isiPublik ? ' class="au-home-footer"' : '' ?>>
		<div class="<?= $kelasFooter ?>">
			<span>&copy; <?= date('Y') ?> LBQ Al-Utsmani &mdash; Learning Management System</span>
			<span>Waktu Asia/Jakarta &middot; Nominal dalam Rupiah</span>
		</div>
	</footer>

<?php if ($isiAdmin): ?>
	</div><!-- /kolom konten admin (offset sidebar) -->
<?php endif; ?>

<script>
(function () {
	var drawer = document.getElementById('adminDrawer');
	if (! drawer) { return; }
	var tombol = document.getElementById('drawerOpen');
	function buka() { drawer.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
	function tutup() { drawer.classList.add('hidden'); document.body.style.overflow = ''; }
	if (tombol) { tombol.addEventListener('click', buka); }
	Array.prototype.forEach.call(drawer.querySelectorAll('[data-drawer-close]'), function (el) {
		el.addEventListener('click', tutup);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { tutup(); }
	});
})();

// Drawer menu publik (< lg) — hanya ada di halaman non-admin.
(function () {
	var drawer = document.getElementById('navDrawer');
	if (! drawer) { return; }
	var tombol = document.getElementById('navDrawerOpen');
	function buka() {
		drawer.classList.remove('hidden');
		document.body.style.overflow = 'hidden';
		if (tombol) { tombol.setAttribute('aria-expanded', 'true'); }
	}
	function tutup() {
		drawer.classList.add('hidden');
		document.body.style.overflow = '';
		if (tombol) { tombol.setAttribute('aria-expanded', 'false'); }
	}
	if (tombol) { tombol.addEventListener('click', buka); }
	Array.prototype.forEach.call(drawer.querySelectorAll('[data-nav-close]'), function (el) {
		el.addEventListener('click', tutup);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { tutup(); }
	});
})();
</script>
</body>
</html>
