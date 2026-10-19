<?php defined('BASEPATH') OR exit('No direct script access allowed');
$kd = $kelas;
$isBilal = ($kd->program_kode === 'BILAL');
$adaBiaya = (int) $kd->biaya_pendaftaran > 0;
?>
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
	<h1 class="page-title">Registrasi Pendaftaran</h1>
	<a href="<?= site_url('daftar/kelas?cabang='.(int) $kd->cabang_id.'&program='.(int) $kd->program_id) ?>" class="text-sm font-semibold text-primary hover:underline">&larr; Pilih kelas lain</a>
</div>

<div class="grid gap-5 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<?php if (empty($user)): ?>
			<section class="card p-5 sm:p-6">
				<h2 class="mb-1 font-semibold text-ink">Buat Akun &amp; Daftarkan Peserta</h2>
				<p class="mb-4 text-sm text-muted">Satu akun bisa menaungi beberapa peserta (mis. orang tua dengan beberapa anak). Data akun dipakai untuk login.</p>

				<?= validation_errors('<div class="mb-3 rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700">', '</div>') ?>

				<?= form_open_multipart('daftar/registrasi/'.$kd->id) ?>
					<h3 class="mb-2 mt-2 text-sm font-semibold text-ink">Data Akun</h3>
					<div class="grid gap-4 sm:grid-cols-2">
						<div>
							<label class="label" for="nama">Nama Lengkap</label>
							<input type="text" id="nama" name="nama" value="<?= html_escape(set_value('nama')) ?>" maxlength="100" required class="input">
						</div>
						<div>
							<label class="label" for="no_wa">No. WhatsApp</label>
							<input type="text" id="no_wa" name="no_wa" value="<?= html_escape(set_value('no_wa')) ?>" maxlength="20" placeholder="0812xxxxxxx" required class="input">
						</div>
						<div>
							<label class="label" for="username">Username</label>
							<input type="text" id="username" name="username" value="<?= html_escape(set_value('username')) ?>" maxlength="50"
								pattern="[A-Za-z0-9_.]{3,50}" title="3–50 karakter: huruf, angka, titik, atau garis bawah" required class="input">
						</div>
						<div>
							<label class="label" for="email">Email</label>
							<input type="email" id="email" name="email" value="<?= html_escape(set_value('email')) ?>" maxlength="100" required class="input">
						</div>
						<div>
							<label class="label" for="password">Password</label>
							<input type="password" id="password" name="password" required class="input">
							<p class="mt-1 text-xs text-muted">Minimal 8 karakter.</p>
						</div>
						<div>
							<label class="label" for="konfirmasi_password">Konfirmasi Password</label>
							<input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="input">
						</div>
					</div>

					<h3 class="mb-2 mt-6 text-sm font-semibold text-ink">Data Peserta <span class="font-normal text-muted">(opsional, dapat dilengkapi nanti)</span></h3>
					<div class="grid gap-4 sm:grid-cols-2">
						<div>
							<label class="label" for="tanggal_lahir">Tanggal Lahir</label>
							<input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= html_escape(set_value('tanggal_lahir')) ?>" class="input">
						</div>
						<div>
							<label class="label" for="jenis_kelamin">Jenis Kelamin</label>
							<select id="jenis_kelamin" name="jenis_kelamin" class="input">
								<option value="">— Pilih —</option>
								<option value="L" <?= set_value('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
								<option value="P" <?= set_value('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
							</select>
						</div>
						<div>
							<label class="label" for="nama_wali">Nama Wali (bila peserta anak)</label>
							<input type="text" id="nama_wali" name="nama_wali" value="<?= html_escape(set_value('nama_wali')) ?>" maxlength="100" class="input">
						</div>
						<div>
							<label class="label" for="kemampuan_awal">Kemampuan Awal</label>
							<select id="kemampuan_awal" name="kemampuan_awal" class="input">
								<option value="pemula" <?= set_value('kemampuan_awal') === 'hafalan' ? '' : 'selected' ?>>Pemula</option>
								<option value="hafalan" <?= set_value('kemampuan_awal') === 'hafalan' ? 'selected' : '' ?>>Sudah punya hafalan</option>
							</select>
							<p class="mt-1 text-xs text-muted">Dipakai pengelola untuk menetapkan level (penempatan).</p>
						</div>
						<div class="sm:col-span-2">
							<label class="label" for="alamat">Alamat</label>
							<input type="text" id="alamat" name="alamat" value="<?= html_escape(set_value('alamat')) ?>" maxlength="255" class="input">
						</div>
					</div>

					<?php if ($adaBiaya): ?>
						<h3 class="mb-2 mt-6 text-sm font-semibold text-ink">Bukti Pembayaran Pendaftaran</h3>
						<input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
							class="block w-full rounded-xl border border-line bg-white px-3 py-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-primary-soft file:px-4 file:py-1.5 file:text-sm file:font-semibold file:text-primary">
						<p class="mt-1 text-xs text-muted">JPG/PNG/PDF maksimal 2 MB. Boleh dikosongkan — Anda dapat mengunggahnya setelah login di halaman Pendaftaran.</p>
					<?php endif; ?>

					<button type="submit" class="btn-primary mt-6 w-full">
						<?= $adaBiaya ? 'Daftar &amp; Kirim Bukti' : 'Daftar Sekarang' ?>
					</button>
				<?= form_close() ?>

				<p class="mt-4 text-center text-sm text-muted">
					Sudah punya akun? <a href="<?= site_url('auth/login') ?>" class="font-semibold text-primary hover:underline">Login</a> lalu daftar dari halaman ini.
				</p>
			</section>

		<?php elseif ($user['role'] === 'peserta'): ?>
			<?php if (! $profilList): ?>
				<section class="card p-5">
					<p class="mb-3 text-sm text-muted">Lengkapi data peserta (Anda/anak) sebelum mendaftar kelas.</p>
					<a href="<?= site_url('peserta/profil/tambah') ?>" class="btn-primary">Tambah Data Peserta</a>
				</section>
			<?php else: ?>
				<section class="card p-5 sm:p-6">
					<h2 class="mb-3 font-semibold text-ink">Pilih Peserta yang Didaftarkan</h2>
					<?= form_open_multipart('daftar/registrasi/'.$kd->id) ?>
						<div class="mb-4">
							<label class="label" for="peserta_id">Peserta</label>
							<select id="peserta_id" name="peserta_id" required class="input">
								<?php foreach ($profilList as $profil): ?>
									<option value="<?= (int) $profil->id ?>" <?= ! empty($sudahDaftar[(int) $profil->id]) ? 'disabled' : '' ?>>
										<?= html_escape($profil->nama) ?><?= ! empty($sudahDaftar[(int) $profil->id]) ? ' (sudah terdaftar)' : '' ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<p class="mb-1 text-sm text-muted">Biaya pendaftaran: <strong class="text-ink"><?= rupiah($kd->biaya_pendaftaran) ?></strong></p>
						<?php if ($adaBiaya): ?>
							<div class="mt-3">
								<label class="label" for="bukti">Bukti Pembayaran (opsional)</label>
								<input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
									class="block w-full rounded-xl border border-line bg-white px-3 py-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-primary-soft file:px-4 file:py-1.5 file:text-sm file:font-semibold file:text-primary">
								<p class="mt-1 text-xs text-muted">JPG/PNG/PDF maksimal 2 MB. Boleh dikosongkan dan diunggah nanti di halaman Pendaftaran.</p>
							</div>
						<?php else: ?>
							<p class="mb-3 text-xs text-muted">Tanpa biaya pendaftaran — pendaftaran langsung aktif.</p>
						<?php endif; ?>
						<button type="submit" class="btn-primary mt-4 w-full">
							<?= $adaBiaya ? 'Daftar &amp; Kirim Bukti' : 'Daftar Kelas' ?>
						</button>
					<?= form_close() ?>
				</section>
			<?php endif; ?>
		<?php else: ?>
			<section class="card p-5">
				<p class="text-sm text-muted">Anda login sebagai <strong class="text-ink"><?= html_escape($user['role']) ?></strong>. Pendaftaran kelas dilakukan oleh akun peserta/wali.</p>
			</section>
		<?php endif; ?>
	</div>

	<aside class="space-y-5">
		<section class="card p-5">
			<div class="mb-3 flex items-center gap-2">
				<span class="badge bg-primary-soft text-primary"><?= html_escape($kd->program_kode) ?></span>
				<span class="text-sm font-semibold text-ink"><?= html_escape($kd->program_nama) ?></span>
			</div>
			<dl class="divide-y divide-line text-sm">
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Level Kelas</dt><dd class="text-ink"><?= html_escape($kd->level_nama) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Cabang</dt><dd class="text-ink"><?= html_escape($kd->cabang_nama) ?> (<?= html_escape($kd->cabang_wilayah) ?>)</dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Mode</dt><dd class="text-ink"><?= ucfirst($kd->mode) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Guru</dt><dd class="text-ink"><?= html_escape($kd->guru_nama) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Jadwal</dt><dd class="text-ink"><?= html_escape($kd->jadwal) ?> <span class="text-slate-400">(<?= html_escape($kd->zona_waktu) ?>)</span></dd></div>
				<?php if ($isBilal): ?>
					<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Tipe Peserta</dt><dd class="text-ink"><?= html_escape(ucfirst($kd->tipe_peserta)) ?><?= $kd->nama_instansi ? ' — '.html_escape($kd->nama_instansi) : '' ?></dd></div>
				<?php endif; ?>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">Pendaftaran</dt><dd class="font-semibold text-ink"><?= rupiah($kd->biaya_pendaftaran) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-28 shrink-0 text-muted">SPP / bulan</dt><dd class="font-semibold text-ink"><?= rupiah($kd->biaya_spp) ?></dd></div>
			</dl>
		</section>

		<section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-xs leading-relaxed text-emerald-900">
			<strong>Soal level/rank:</strong> level kelas ini adalah target pembelajaran. Level Anda ditetapkan pengelola melalui proses penempatan, bukan dipilih saat mendaftar.
		</section>
	</aside>
</div>
