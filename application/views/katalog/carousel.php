<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Carousel katalog program. Dipakai beranda & halaman katalog.
 * Variabel: $programs, $rekap (dari Kelas_model::rekapProgram()).
 */
$adaProgram = FALSE;
foreach ($programs as $p) {
	if (isset($rekap[(int) $p->id]) && $rekap[(int) $p->id]['jumlah'] > 0) {
		$adaProgram = TRUE;
		break;
	}
}
?>
<div class="relative" data-carousel>
	<div class="mb-3 flex items-center justify-between gap-2">
		<p class="text-sm text-muted">Geser untuk melihat program lainnya.</p>
		<div class="flex gap-2">
			<button type="button" data-prev aria-label="Program sebelumnya"
				class="h-10 w-10 rounded-full border border-line bg-white text-muted transition-colors hover:bg-canvas disabled:opacity-40">&#8592;</button>
			<button type="button" data-next aria-label="Program berikutnya"
				class="h-10 w-10 rounded-full border border-line bg-white text-muted transition-colors hover:bg-canvas disabled:opacity-40">&#8594;</button>
		</div>
	</div>

	<div data-track class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2">
		<?php foreach ($programs as $p): $pid = (int) $p->id; $info = isset($rekap[$pid]) ? $rekap[$pid] : array('jumlah' => 0, 'cabang' => array()); ?>
			<?php $gambar = program_gambar($p->kode); ?>
			<article class="card flex w-[86%] shrink-0 snap-start flex-col overflow-hidden sm:w-[47%] lg:w-[31.5%]">
				<div class="relative h-52">
					<?php if ($gambar): ?>
						<img src="<?= html_escape($gambar) ?>" alt="Program <?= html_escape($p->nama) ?>" loading="lazy" class="h-full w-full object-cover">
					<?php else: ?>
						<div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary to-primary-deep">
							<span class="text-5xl font-black tracking-wider text-white/90"><?= html_escape($p->kode) ?></span>
						</div>
					<?php endif; ?>
					<span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold text-primary"><?= html_escape($p->kode) ?></span>
				</div>
				<div class="flex flex-1 flex-col p-6">
					<h3 class="text-lg font-bold text-ink"><?= html_escape($p->nama) ?></h3>
					<p class="mt-1 flex-1 text-sm leading-relaxed text-muted"><?= html_escape($p->deskripsi) ?></p>

					<?php $kelasInfo = isset($info['kelas']) ? $info['kelas'] : array(); ?>
					<div class="mt-3 text-xs text-muted">
						<div><span class="font-semibold text-ink"><?= (int) $info['jumlah'] ?></span> kelas aktif</div>
						<?php if ($kelasInfo): ?>
							<ul class="mt-2 space-y-1">
								<?php $i = 0; foreach ($kelasInfo as $k): if ($i++ >= 3) { break; } ?>
									<?= baris_kelas_html($k) ?>
								<?php endforeach; ?>
								<?php if (count($kelasInfo) > 3): ?>
									<li class="text-xs text-slate-400">+<?= count($kelasInfo) - 3 ?> kelas lainnya</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>
					</div>

					<div class="mt-4 flex gap-2">
						<a href="<?= site_url('daftar/kelas?program='.$pid) ?>" class="btn-primary btn-sm flex-1">Daftar</a>
						<a href="<?= site_url('katalog/program/'.$pid) ?>" class="btn-outline btn-sm">Detail</a>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<div data-dots class="mt-3 flex justify-center gap-2"></div>
	<?php if (! $adaProgram): ?>
		<p class="mt-2 text-center text-sm text-muted">Belum ada kelas aktif. Silakan cek kembali nanti.</p>
	<?php endif; ?>
</div>

<script>
(function () {
	document.querySelectorAll('[data-carousel]').forEach(function (root) {
		var track = root.querySelector('[data-track]');
		var slides = track ? track.children : [];
		var prev = root.querySelector('[data-prev]');
		var next = root.querySelector('[data-next]');
		var dots = root.querySelector('[data-dots]');
		if (! track || ! slides.length) { return; }

		function lebar() {
			return slides[0].getBoundingClientRect().width + 16;
		}
		function index() {
			return Math.round(track.scrollLeft / lebar());
		}
		function ke(i) {
			var maks = Math.max(0, slides.length - 1);
			i = Math.max(0, Math.min(maks, i));
			track.scrollTo({ left: i * lebar(), behavior: 'smooth' });
		}
		function perbarui() {
			var i = index();
			Array.prototype.forEach.call(dots.children, function (d, j) {
				d.className = 'h-2 w-2 rounded-full ' + (j === i ? 'bg-primary' : 'bg-slate-300');
			});
			if (prev) { prev.disabled = i <= 0; }
			if (next) { next.disabled = i >= slides.length - 1; }
		}

		for (var i = 0; i < slides.length; i++) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'Ke program ' + (i + 1));
			(function (n, el) { el.addEventListener('click', function () { ke(n); }); })(i, b);
			dots.appendChild(b);
		}

		if (prev) { prev.addEventListener('click', function () { ke(index() - 1); }); }
		if (next) { next.addEventListener('click', function () { ke(index() + 1); }); }

		var timer = null;
		track.addEventListener('scroll', function () {
			window.clearTimeout(timer);
			timer = window.setTimeout(perbarui, 80);
		});
		perbarui();
	});
})();
</script>
