<?php defined('BASEPATH') OR exit('No direct script access allowed');
$kd = $kelas;
$isBilal = ($kd->program_kode === 'BILAL');
$adaBiaya = (int) $kd->biaya_pendaftaran > 0;
?>
<div class="au-home-container">
	<header class="au-page-head">
		<div class="au-page-head__text">
			<p class="au-home-eyebrow">Pendaftaran</p>
			<h1 class="au-home-heading au-home-heading--h1">Registrasi Pendaftaran</h1>
			<p class="au-home-body au-home-body--lg"><?= html_escape($kd->program_kode) ?> &middot; <?= html_escape($kd->level_nama) ?> &middot; <?= html_escape($kd->cabang_nama) ?></p>
		</div>
		<div class="au-page-head__actions">
			<a href="<?= site_url('daftar/kelas?cabang='.(int) $kd->cabang_id.'&program='.(int) $kd->program_id) ?>" class="au-home-button au-home-button-secondary">
				<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
				Pilih kelas lain
			</a>
		</div>
	</header>

	<div class="au-detail-grid">
		<div class="au-detail-stack">
			<?php if (empty($user)): ?>
				<section class="au-panel">
					<div class="au-panel__head">
						<h2 class="au-panel__title">Buat akun &amp; daftarkan peserta</h2>
					</div>
					<div class="au-panel__body">
						<p class="au-home-body mb-4">Satu akun bisa menaungi beberapa peserta (mis. orang tua dengan beberapa anak). Data akun dipakai untuk login.</p>

						<?php if (validation_errors()): ?>
							<div class="au-form-errors"><?= validation_errors('<p class="au-error">', '</p>') ?></div>
						<?php endif; ?>

						<?= form_open_multipart('daftar/registrasi/'.$kd->id) ?>
							<h3 class="au-home-heading au-home-heading--h3 mb-3">Data akun</h3>
							<div class="au-form-grid">
								<div class="au-field">
									<label class="au-label" for="nama">Nama Lengkap</label>
									<input type="text" id="nama" name="nama" value="<?= html_escape(set_value('nama')) ?>" maxlength="100" required class="au-input">
								</div>
								<div class="au-field">
									<label class="au-label" for="no_wa">No. WhatsApp</label>
									<input type="text" id="no_wa" name="no_wa" value="<?= html_escape(set_value('no_wa')) ?>" maxlength="20" placeholder="0812xxxxxxx" required class="au-input">
								</div>
								<div class="au-field">
									<label class="au-label" for="username">Username</label>
									<input type="text" id="username" name="username" value="<?= html_escape(set_value('username')) ?>" maxlength="50"
										pattern="[A-Za-z0-9_.]{3,50}" title="3–50 karakter: huruf, angka, titik, atau garis bawah" required class="au-input">
								</div>
								<div class="au-field">
									<label class="au-label" for="email">Email</label>
									<input type="email" id="email" name="email" value="<?= html_escape(set_value('email')) ?>" maxlength="100" required class="au-input">
								</div>
								<div class="au-field">
									<label class="au-label" for="password">Password</label>
									<input type="password" id="password" name="password" required class="au-input">
									<p class="au-hint">Minimal 8 karakter.</p>
								</div>
								<div class="au-field">
									<label class="au-label" for="konfirmasi_password">Konfirmasi Password</label>
									<input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="au-input">
								</div>
							</div>

							<h3 class="au-home-heading au-home-heading--h3 mb-1 mt-6">Data peserta</h3>
							<p class="au-hint mb-3">Opsional, dapat dilengkapi nanti.</p>
							<div class="au-form-grid">
								<div class="au-field">
									<label class="au-label" for="tanggal_lahir">Tanggal Lahir</label>
									<input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= html_escape(set_value('tanggal_lahir')) ?>" class="au-input">
								</div>
								<div class="au-field">
									<label class="au-label" for="jenis_kelamin">Jenis Kelamin</label>
									<select id="jenis_kelamin" name="jenis_kelamin" class="au-select">
										<option value="">— Pilih —</option>
										<option value="L" <?= set_value('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
										<option value="P" <?= set_value('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
									</select>
								</div>
								<div class="au-field">
									<label class="au-label" for="nama_wali">Nama Wali</label>
									<input type="text" id="nama_wali" name="nama_wali" value="<?= html_escape(set_value('nama_wali')) ?>" maxlength="100" class="au-input">
									<p class="au-hint">Diisi bila peserta adalah anak.</p>
								</div>
								<div class="au-field">
									<label class="au-label" for="kemampuan_awal">Kemampuan Awal</label>
									<select id="kemampuan_awal" name="kemampuan_awal" class="au-select">
										<option value="pemula" <?= set_value('kemampuan_awal') === 'hafalan' ? '' : 'selected' ?>>Pemula</option>
										<option value="hafalan" <?= set_value('kemampuan_awal') === 'hafalan' ? 'selected' : '' ?>>Sudah punya hafalan</option>
									</select>
									<p class="au-hint">Dipakai pengelola untuk menetapkan level (penempatan).</p>
								</div>
								<div class="au-field au-field--full">
									<label class="au-label" for="alamat">Alamat</label>
									<input type="text" id="alamat" name="alamat" value="<?= html_escape(set_value('alamat')) ?>" maxlength="255" class="au-input">
								</div>
							</div>

							<?php if ($adaBiaya): ?>
								<h3 class="au-home-heading au-home-heading--h3 mb-2 mt-6">Bukti pembayaran pendaftaran</h3>
								<input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="au-file">
								<p class="au-hint mt-2">JPG/PNG/PDF maksimal 2 MB. Boleh dikosongkan — Anda dapat mengunggahnya setelah login di halaman Pendaftaran.</p>
							<?php endif; ?>

							<button type="submit" class="au-home-button au-home-button-primary au-home-button--block mt-6">
								<?= $adaBiaya ? 'Daftar &amp; Kirim Bukti' : 'Daftar Sekarang' ?>
							</button>
						<?= form_close() ?>

						<p class="au-auth__switch">
							Sudah punya akun? <a href="<?= site_url('auth/login') ?>">Login</a> lalu daftar dari halaman ini.
						</p>
					</div>
				</section>

			<?php elseif ($user['role'] === 'peserta'): ?>
				<?php if (! $profilList): ?>
					<section class="au-panel">
						<div class="au-panel__body">
							<p class="au-home-body mb-4">Lengkapi data peserta (Anda/anak) sebelum mendaftar kelas.</p>
							<a href="<?= site_url('peserta/profil/tambah') ?>" class="au-home-button au-home-button-primary au-home-button--sm">Tambah Data Peserta</a>
						</div>
					</section>
				<?php else: ?>
					<section class="au-panel">
						<div class="au-panel__head">
							<h2 class="au-panel__title">Pilih peserta yang didaftarkan</h2>
						</div>
						<div class="au-panel__body">
							<?= form_open_multipart('daftar/registrasi/'.$kd->id) ?>
								<div class="au-field">
									<label class="au-label" for="peserta_id">Peserta</label>
									<select id="peserta_id" name="peserta_id" required class="au-select">
										<?php foreach ($profilList as $profil): ?>
											<option value="<?= (int) $profil->id ?>" <?= ! empty($sudahDaftar[(int) $profil->id]) ? 'disabled' : '' ?>>
												<?= html_escape($profil->nama) ?><?= ! empty($sudahDaftar[(int) $profil->id]) ? ' (sudah terdaftar)' : '' ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

								<?php if ($adaBiaya): ?>
									<div class="au-field mt-4">
										<label class="au-label" for="bukti">Bukti Pembayaran (opsional)</label>
										<input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="au-file">
										<p class="au-hint">JPG/PNG/PDF maksimal 2 MB. Boleh dikosongkan dan diunggah nanti di halaman Pendaftaran.</p>
									</div>
								<?php else: ?>
									<p class="au-hint mt-4">Tanpa biaya pendaftaran — pendaftaran langsung aktif.</p>
								<?php endif; ?>

								<button type="submit" class="au-home-button au-home-button-primary au-home-button--block mt-5">
									<?= $adaBiaya ? 'Daftar &amp; Kirim Bukti' : 'Daftar Kelas' ?>
								</button>
							<?= form_close() ?>
						</div>
					</section>
				<?php endif; ?>
			<?php else: ?>
				<section class="au-panel">
					<div class="au-panel__body">
						<p class="au-home-body">Anda login sebagai <strong><?= html_escape($user['role']) ?></strong>. Pendaftaran kelas dilakukan oleh akun peserta/wali.</p>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<aside class="au-detail-stack au-detail-stack--sticky">
			<section class="au-panel">
				<div class="au-panel__head">
					<h2 class="au-panel__title">Ringkasan kelas</h2>
					<span class="au-chip au-chip--brand"><?= html_escape($kd->program_kode) ?></span>
				</div>
				<div class="au-panel__body">
					<p class="au-home-heading au-home-heading--h3 mb-1"><?= html_escape($kd->program_nama) ?></p>
					<p class="au-home-caption mb-3"><?= html_escape($kd->level_nama) ?> &middot; <?= html_escape($kd->cabang_nama) ?></p>
					<dl class="au-detail-list">
						<div><dt>Mode</dt><dd><?= ucfirst($kd->mode) ?></dd></div>
						<div><dt>Guru</dt><dd><?= html_escape($kd->guru_nama) ?></dd></div>
						<div><dt>Jadwal</dt><dd><?= html_escape(jadwal_tampil($kd)) ?></dd></div>
						<?php if ($isBilal): ?>
							<div><dt>Tipe peserta</dt><dd><?= html_escape(ucfirst($kd->tipe_peserta)) ?><?= $kd->nama_instansi ? ' — '.html_escape($kd->nama_instansi) : '' ?></dd></div>
						<?php endif; ?>
						<div><dt>Pendaftaran</dt><dd><?= rupiah($kd->biaya_pendaftaran) ?></dd></div>
						<div><dt>SPP / bulan</dt><dd><?= rupiah($kd->biaya_spp) ?></dd></div>
					</dl>
				</div>
			</section>

			<section class="au-notice">
				<strong>Soal level/rank:</strong> level kelas ini adalah target pembelajaran. Level Anda ditetapkan pengelola melalui proses penempatan, bukan dipilih saat mendaftar.
			</section>
		</aside>
	</div>
</div>
