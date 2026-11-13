<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Carousel katalog program. Dipakai halaman katalog.
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
<div class="au-carousel" data-carousel>
	<div class="au-carousel__bar">
		<p class="au-home-caption">Geser untuk melihat program lainnya.</p>
		<div class="flex gap-2">
			<button type="button" data-prev aria-label="Program sebelumnya" class="au-carousel__nav">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
			</button>
			<button type="button" data-next aria-label="Program berikutnya" class="au-carousel__nav">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
			</button>
		</div>
	</div>

	<div data-track class="au-carousel__track">
		<?php foreach ($programs as $p): $pid = (int) $p->id; $info = isset($rekap[$pid]) ? $rekap[$pid] : array('jumlah' => 0, 'cabang' => array()); ?>
			<?php $gambar = program_gambar($p->kode); ?>
			<article class="au-home-card au-carousel__slide flex flex-col">
				<div class="au-carousel__media">
					<?php if ($gambar): ?>
						<img src="<?= html_escape($gambar) ?>" alt="Program <?= html_escape($p->nama) ?>" loading="lazy">
					<?php else: ?>
						<div class="au-program-card__fallback">
							<span><?= html_escape($p->kode) ?></span>
						</div>
					<?php endif; ?>
					<span class="au-program-card__kode"><?= html_escape($p->kode) ?></span>
				</div>
				<div class="au-panel__body flex flex-1 flex-col">
					<h3 class="au-home-heading au-home-heading--h3"><?= html_escape($p->nama) ?></h3>
					<p class="au-home-body mt-1 flex-1"><?= html_escape($p->deskripsi) ?></p>

					<?php $kelasInfo = isset($info['kelas']) ? $info['kelas'] : array(); ?>
					<div class="mt-3">
						<p class="au-home-caption"><strong class="text-ink"><?= (int) $info['jumlah'] ?></strong> kelas aktif</p>
						<?php if ($kelasInfo): ?>
							<ul class="au-program-card__list mt-2">
								<?php $i = 0; foreach ($kelasInfo as $k): if ($i++ >= 3) { break; } ?>
									<?= baris_kelas_html($k) ?>
								<?php endforeach; ?>
								<?php if (count($kelasInfo) > 3): ?>
									<li class="au-home-caption">+<?= count($kelasInfo) - 3 ?> kelas lainnya</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>
					</div>

					<div class="au-kelas-card__actions mt-4">
						<a href="<?= site_url('daftar/kelas?program='.$pid) ?>" class="au-home-button au-home-button-primary au-home-button--sm">Daftar</a>
						<a href="<?= site_url('katalog/program/'.$pid) ?>" class="au-home-button au-home-button-secondary au-home-button--sm">Detail</a>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<div data-dots class="au-carousel__dots"></div>
	<?php if (! $adaProgram): ?>
		<p class="au-home-caption mt-2 text-center">Belum ada kelas aktif. Silakan cek kembali nanti.</p>
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

		function jarak() {
			var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 16;
			return gap;
		}
		function lebar() {
			return slides[0].getBoundingClientRect().width + jarak();
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
				d.className = 'au-carousel__dot' + (j === i ? ' au-carousel__dot--active' : '');
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
