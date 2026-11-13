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
<label class="au-program-card" data-opsi-item data-program="<?= $pid ?>">
	<input type="radio" name="program" value="<?= $pid ?>" class="sr-only">
	<span class="au-program-card__media" aria-hidden="true">
		<?php if ($gambar): ?>
			<img src="<?= html_escape($gambar) ?>" alt="" loading="lazy">
		<?php else: ?>
			<span class="au-program-card__fallback"><?= html_escape($p->kode) ?></span>
		<?php endif; ?>
		<span class="au-program-card__kode"><?= html_escape($p->kode) ?></span>
		<span class="au-program-card__jumlah"><?= (int) $info['jumlah'] ?> kelas</span>
	</span>
	<span class="au-program-card__body">
		<span class="au-program-card__title"><?= html_escape($p->nama) ?></span>
		<ul data-kelas-list data-maks="<?= $maks ?>" class="au-program-card__list">
			<?php if ($kelas): ?>
				<?php $i = 0; foreach ($kelas as $k): if ($i++ >= $maks) { break; } ?>
					<?= baris_kelas_html($k) ?>
				<?php endforeach; ?>
				<?php if (count($kelas) > $maks): ?>
					<li class="au-home-caption">+<?= count($kelas) - $maks ?> kelas lainnya</li>
				<?php endif; ?>
			<?php else: ?>
				<li class="au-home-caption">Belum ada kelas aktif.</li>
			<?php endif; ?>
		</ul>
	</span>
</label>
