<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $isiAdmin = (! empty($user) && $user['role'] === 'admin' && $this->uri->segment(1) === 'admin'); ?>
	</main>

	<footer>
		<div class="flex flex-wrap justify-between gap-2 border-t border-line py-6 text-sm text-muted <?= $isiAdmin ? 'px-4 sm:px-6 lg:px-8' : 'mx-auto max-w-6xl px-4' ?>">
			<span>&copy; <?= date('Y') ?> Al-Utsmani &mdash; Learning Management System</span>
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
</script>
</body>
</html>
