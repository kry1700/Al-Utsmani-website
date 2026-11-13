<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Kartu cabang bergambar (dipakai pemilih dua arah di beranda & /daftar).
 * Foto wilayah (kiri) + nama cabang + wilayah. Bila foto wilayah belum
 * tersedia, helper cabang_gambar() mengembalikan NULL → fallback gradient
 * + singkatan nama cabang.
 *
 * Variabel yang diharapkan: $c (baris cabang).
 */
$gambar = cabang_gambar($c->wilayah);
?>
<label class="au-cabang-card" data-opsi-item data-cabang="<?= (int) $c->id ?>">
	<input type="radio" name="cabang" value="<?= (int) $c->id ?>" class="sr-only">
	<span class="au-cabang-card__media" aria-hidden="true">
		<?php if ($gambar): ?>
			<img src="<?= html_escape($gambar) ?>" alt="" loading="lazy">
		<?php else: ?>
			<span class="au-cabang-card__fallback"><?= html_escape(singkatan($c->nama)) ?></span>
		<?php endif; ?>
	</span>
	<span class="au-cabang-card__body">
		<span class="au-cabang-card__nama"><?= html_escape($c->nama) ?></span>
		<span class="au-cabang-card__wilayah"><?= html_escape($c->wilayah) ?></span>
	</span>
	<span class="au-cabang-card__check" aria-hidden="true">
		<svg fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
	</span>
</label>
