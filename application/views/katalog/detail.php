<?php defined('BASEPATH') OR exit('No direct script access allowed');
$kd = $kelas; // alias singkat
$isBilal = ($kd->program_kode === 'BILAL');
?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Detail Kelas &middot; <?= html_escape($kd->program_kode) ?></p>
			<h1 class="au-home-heading au-home-heading--h1"><?= html_escape($kd->level_nama) ?> &mdash; <?= html_escape($kd->cabang_nama) ?></h1>
			<div class="au-detail-badges">
				<span class="au-chip au-chip--brand"><?= html_escape($kd->program_nama) ?></span>
				<span class="au-chip <?= $kd->mode === 'online' ? 'au-chip--online' : 'au-chip--offline' ?>">
					<?= $kd->mode === 'online' ? 'Online' : 'Offline' ?>
				</span>
			</div>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('katalog/program/'.(int) $kd->program_id) ?>" class="au-home-button au-home-button-secondary">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
				Kelas <?= html_escape($kd->program_kode) ?>
			</a>
		</div>
	</header>

	<div class="au-detail-grid">
		<div class="au-detail-stack">
			<section class="au-panel">
				<div class="au-panel__head">
					<h2 class="au-panel__title">Informasi kelas</h2>
				</div>
				<div class="au-panel__body">
					<dl class="au-detail-list">
						<div><dt>Program</dt><dd><?= html_escape($kd->program_nama) ?> (<?= html_escape($kd->program_kode) ?>)</dd></div>
						<div><dt>Level kelas</dt><dd><?= html_escape($kd->level_nama) ?></dd></div>
						<div><dt>Cabang</dt><dd><?= html_escape($kd->cabang_nama) ?> &middot; <?= html_escape($kd->cabang_wilayah) ?></dd></div>
						<div><dt>Guru pengampu</dt><dd><?= html_escape($kd->guru_nama) ?></dd></div>
						<div><dt>Jadwal</dt><dd><?= html_escape(jadwal_tampil($kd)) ?></dd></div>
						<?php if ($isBilal): ?>
							<div>
								<dt>Tipe peserta</dt>
								<dd><?= html_escape(ucfirst($kd->tipe_peserta)) ?><?= ($kd->nama_instansi !== NULL && $kd->nama_instansi !== '') ? ' — '.html_escape($kd->nama_instansi) : '' ?></dd>
							</div>
						<?php endif; ?>
						<div><dt>Biaya pendaftaran</dt><dd><?= rupiah($kd->biaya_pendaftaran) ?></dd></div>
						<div><dt>Biaya SPP / bulan</dt><dd><?= rupiah($kd->biaya_spp) ?></dd></div>
					</dl>
				</div>
			</section>

			<?php if ($kd->mode === 'online'): ?>
				<section class="au-panel">
					<div class="au-panel__head">
						<h2 class="au-panel__title">Link kelas online</h2>
					</div>
					<div class="au-panel__body">
						<?php if ($bolehLihatLink && $kd->link_meeting): ?>
							<?php if (filter_var($kd->link_meeting, FILTER_VALIDATE_URL)): ?>
								<a href="<?= html_escape($kd->link_meeting) ?>" target="_blank" rel="noopener" class="au-home-button au-home-button-primary au-home-button--sm">
									<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
									Buka link meeting
								</a>
								<p class="au-home-caption mt-3 break-all"><?= html_escape($kd->link_meeting) ?></p>
							<?php else: ?>
								<p class="au-home-body"><?= html_escape($kd->link_meeting) ?></p>
							<?php endif; ?>
							<p class="au-home-caption mt-3">Link ini khusus untuk peserta terdaftar dan guru pengampu — mohon dibagikan hanya seperlunya.</p>
						<?php else: ?>
							<p class="au-home-body">Link meeting hanya ditampilkan untuk peserta yang terdaftar pada kelas ini (setelah pendaftaran aktif).</p>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<aside class="au-detail-stack au-detail-stack--sticky">
			<section class="au-panel">
				<div class="au-panel__head">
					<h2 class="au-panel__title">Pendaftaran</h2>
				</div>
				<div class="au-panel__body">
					<?php if (empty($user)): ?>
						<p class="au-home-body mb-4">Daftar sekarang — cukup isi registrasi singkat, akun peserta/wali dibuat otomatis.</p>
						<a href="<?= site_url('daftar/registrasi/'.$kd->id) ?>" class="au-home-button au-home-button-primary au-home-button--block">Daftar Kelas Ini</a>
						<p class="au-auth__switch">
							Sudah punya akun? <a href="<?= site_url('auth/login') ?>">Login</a>
						</p>

					<?php elseif ($user['role'] === 'peserta'): ?>
						<?php if ($pendaftaranSaya): ?>
							<p class="mb-3"><?= badge('pendaftaran', $pendaftaranSaya->status) ?></p>
							<?php if ($pendaftaranSaya->status === 'menunggu' && $kd->biaya_pendaftaran > 0): ?>
								<p class="au-home-body mb-4">Bukti pembayaran Anda sedang menunggu verifikasi admin. Pastikan bukti sudah diunggah di halaman Pendaftaran.</p>
							<?php elseif ($pendaftaranSaya->status === 'menunggu'): ?>
								<p class="au-home-body mb-4">Pendaftaran Anda sedang menunggu verifikasi admin.</p>
							<?php elseif ($pendaftaranSaya->status === 'aktif'): ?>
								<p class="au-home-body mb-4">Anda sudah terdaftar pada kelas ini. Jadwal dan link kelas ada di dashboard Anda.</p>
							<?php endif; ?>
							<a href="<?= site_url('peserta/pendaftaran') ?>" class="au-home-button au-home-button-secondary au-home-button--block">Lihat Pendaftaran Saya</a>

						<?php elseif (empty($profilList)): ?>
							<p class="au-home-body mb-4">Lengkapi data peserta (Anda/anak) sebelum mendaftar kelas.</p>
							<a href="<?= site_url('peserta/profil/tambah') ?>" class="au-home-button au-home-button-secondary au-home-button--block">Tambah Data Peserta</a>

						<?php else: ?>
							<div class="au-kelas-card__price au-kelas-card__price--flush">
								<span>Pendaftaran<strong><?= rupiah($kd->biaya_pendaftaran) ?></strong></span>
								<span>SPP / bulan<strong><?= rupiah($kd->biaya_spp) ?></strong></span>
							</div>
							<p class="au-home-caption mb-4"><?= $kd->biaya_pendaftaran > 0 ? 'Anda akan memilih peserta lalu boleh langsung mengunggah bukti.' : 'Tanpa biaya pendaftaran — pendaftaran langsung aktif.' ?></p>
							<a href="<?= site_url('daftar/registrasi/'.$kd->id) ?>" class="au-home-button au-home-button-primary au-home-button--block">
								<?= $kd->biaya_pendaftaran > 0 ? 'Daftar &amp; Lanjut Pembayaran' : 'Daftar Kelas' ?>
							</a>
						<?php endif; ?>

					<?php else: ?>
						<p class="au-home-body">Anda login sebagai <?= html_escape($user['role']) ?> — pendaftaran dilakukan oleh akun peserta/wali.</p>
					<?php endif; ?>
				</div>
			</section>

			<a href="<?= site_url('katalog') ?>" class="au-home-button au-home-button-secondary au-home-button--block">&larr; Kembali ke Katalog</a>
		</aside>
	</div>
</div>
