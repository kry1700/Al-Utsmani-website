<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Kartu program bergambar (dipakai pemilih dua arah di beranda & /daftar).
 * Gambar utama → kode program (BIHAR) → nama program → daftar kelas & jam.
 *
 * Variabel yang diharapkan: $p (baris program), $rekap (Kelas_model::rekapProgram()),
 * $maksBaris (int, batas baris kelas yang tampil; default 3).
 * Kartu ini merender data dari SEMUA cabang; daftar kelasnya di-render ulang
 * oleh JS (data-pilih) saat cabang dipilih.
 */
$pid = (int) $p->id;
$info = isset($rekap[$pid]) ? $rekap[$pid] : array('jumlah' => 0, 'cabang' => array(), 'kelas' => array());
$kelas = isset($info['kelas']) ? $info['kelas'] : array();
$maks = isset($maksBaris) ? (int) $maksBaris : 3;
$gambar = program_gambar($p->kode);
?>
<label class="cursor-pointer group" data-opsi-item data-program="<?= $pid ?>">
	<input type="radio" name="program" value="<?= $pid ?>" class="peer sr-only">
	<div class="flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-white
		peer-checked:border-emerald-600 peer-checked:ring-2 peer-checked:ring-emerald-200
		peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-400 group-hover:border-emerald-400">
		<div class="h-36 relative">
			<?php if ($gambar): ?>
				<img src="<?= html_escape($gambar) ?>" alt="Program <?= html_escape($p->nama) ?>" loading="lazy" class="w-full h-full object-cover">
			<?php else: ?>
				<div class="w-full h-full bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center">
					<span class="text-3xl font-black text-white/90 tracking-wider"><?= html_escape($p->kode) ?></span>
				</div>
			<?php endif; ?>
			<span class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-xs font-bold text-emerald-800"><?= html_escape($p->kode) ?></span>
			<span class="absolute bottom-2 right-2 rounded-full bg-slate-900/70 px-1.5 py-0.5 text-[10px] font-medium text-white"><?= (int) $info['jumlah'] ?> kelas</span>
		</div>
		<div class="flex flex-1 flex-col p-4">
			<div class="text-base font-semibold text-ink"><?= html_escape($p->nama) ?></div>
			<ul data-kelas-list data-maks="<?= $maks ?>" class="mt-2 space-y-1">
				<?php if ($kelas): ?>
					<?php $i = 0; foreach ($kelas as $k): if ($i++ >= $maks) { break; } ?>
						<?= baris_kelas_html($k) ?>
					<?php endforeach; ?>
					<?php if (count($kelas) > $maks): ?>
						<li class="text-xs text-slate-400">+<?= count($kelas) - $maks ?> kelas lainnya</li>
					<?php endif; ?>
				<?php else: ?>
					<li class="text-xs text-slate-400">Belum ada kelas aktif.</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</label>
