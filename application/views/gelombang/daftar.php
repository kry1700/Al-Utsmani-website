<?php defined('BASEPATH') OR exit('No direct script access allowed');
$emailAwal = isset($email_akun) ? (string) $email_akun : '';
?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Pendaftaran &middot; <?= html_escape($gelombang->angkatan) ?></p>
			<h1 class="au-home-heading au-home-heading--h1">Daftar Minat</h1>
			<p class="au-home-body au-home-body--lg">
				<?= html_escape($gelombang->program_kode.' - '.$gelombang->program_nama) ?>
				&middot; <?= tanggal_indo($gelombang->tgl_buka) ?> s/d <?= tanggal_indo($gelombang->tgl_tutup) ?>
			</p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('gelombang') ?>" class="au-home-button au-home-button-secondary">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
				Semua gelombang
			</a>
		</div>
	</header>

	<div class="au-form-page">
		<section class="au-panel">
			<div class="au-panel__head">
				<h2 class="au-panel__title">Formulir pendaftaran minat</h2>
			</div>
			<div class="au-panel__body">
				<?php if (validation_errors()): ?>
					<div class="au-form-errors"><?= validation_errors('<p class="au-error">', '</p>') ?></div>
				<?php endif; ?>

				<?= form_open('gelombang/daftar/'.$gelombang->id, array('class' => 'au-form-stack')) ?>
					<div class="au-field">
						<label class="au-label" for="nama_calon">Nama Calon Peserta <span aria-hidden="true">*</span></label>
						<input type="text" id="nama_calon" name="nama_calon" value="<?= html_escape(set_value('nama_calon', isset($peserta->nama) ? $peserta->nama : '')) ?>" required class="au-input">
					</div>
					<div class="au-field">
						<label class="au-label" for="no_hp">No. HP <span aria-hidden="true">*</span></label>
						<input type="text" id="no_hp" name="no_hp" value="<?= html_escape(set_value('no_hp', isset($peserta->no_hp) ? $peserta->no_hp : '')) ?>" required class="au-input">
					</div>
					<div class="au-field">
						<label class="au-label" for="email">Email</label>
						<input type="email" id="email" name="email" value="<?= html_escape(set_value('email', $emailAwal)) ?>" class="au-input">
						<p class="au-hint">Boleh dikosongkan bila tidak ada — admin akan menghubungi lewat No. HP.</p>
					</div>

					<div class="au-pilih-aksi">
						<button type="submit" class="au-home-button au-home-button-primary">Kirim Pendaftaran Minat</button>
						<a href="<?= site_url('gelombang') ?>" class="au-home-button au-home-button-secondary">Batal</a>
					</div>
				<?= form_close() ?>
			</div>
		</section>
	</div>
</div>
