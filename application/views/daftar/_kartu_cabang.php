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
<label class="cursor-pointer group" data-opsi-item data-cabang="<?= (int) $c->id ?>">
	<input type="radio" name="cabang" value="<?= (int) $c->id ?>" class="peer sr-only">
	<div class="flex items-center gap-3 rounded-xl border border-line bg-white p-2
		peer-checked:border-emerald-600 peer-checked:ring-2 peer-checked:ring-emerald-200
		peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-400 group-hover:border-emerald-400">
		<div class="h-12 w-16 shrink-0 rounded overflow-hidden">
			<?php if ($gambar): ?>
				<img src="<?= html_escape($gambar) ?>" alt="<?= html_escape($c->wilayah) ?>" loading="lazy" class="w-full h-full object-cover">
			<?php else: ?>
				<div class="w-full h-full bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center">
					<span class="text-xs font-black text-white/90 tracking-wider"><?= html_escape(singkatan($c->nama)) ?></span>
				</div>
			<?php endif; ?>
		</div>
		<div class="min-w-0">
			<div class="truncate text-sm font-medium text-ink"><?= html_escape($c->nama) ?></div>
			<div class="truncate text-xs text-muted"><?= html_escape($c->wilayah) ?></div>
		</div>
	</div>
</label>
