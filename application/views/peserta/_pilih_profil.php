<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Partial: pemilih profil peserta (dipakai halaman riwayat).
 * Variabel: $profil (terpilih), $aksi (URL tujuan), $profilList (daftar profil).
 */
?>
<?php if (count($profilList) > 1): ?>
	<div class="card mb-5 flex flex-wrap items-end gap-3 p-5">
		<form method="get" action="<?= site_url($aksi) ?>" class="flex flex-wrap items-end gap-3">
			<div>
				<label class="label" for="peserta">Pilih Peserta</label>
				<select id="peserta" name="peserta" class="input w-auto" onchange="this.form.submit()">
					<?php foreach ($profilList as $p): ?>
						<option value="<?= (int) $p->id ?>" <?= (int) $p->id === (int) $profil->id ? 'selected' : '' ?>>
							<?= html_escape($p->nama) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<noscript><button type="submit" class="btn-primary">Tampilkan</button></noscript>
		</form>
	</div>
<?php endif; ?>
