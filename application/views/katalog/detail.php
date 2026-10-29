<?php defined('BASEPATH') OR exit('No direct script access allowed');
$kd = $kelas; // alias singkat
$isBilal = ($kd->program_kode === 'BILAL');
?>
<div class="grid gap-5 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div class="card p-6">
			<div class="mb-4 flex flex-wrap items-center gap-2">
				<span class="badge bg-primary-soft text-primary"><?= html_escape($kd->program_kode) ?></span>
				<h1 class="text-xl font-bold text-ink sm:text-2xl"><?= html_escape($kd->level_nama) ?> — <?= html_escape($kd->cabang_nama) ?></h1>
				<span class="badge <?= $kd->mode === 'online' ? 'bg-sky-100 text-sky-800' : 'bg-violet-100 text-violet-800' ?>">
					<?= $kd->mode === 'online' ? 'Online' : 'Offline' ?>
				</span>
			</div>

			<dl class="divide-y divide-line text-sm">
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Program</dt>
					<dd class="text-ink"><?= html_escape($kd->program_nama) ?> (<?= html_escape($kd->program_kode) ?>)</dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Level</dt>
					<dd class="text-ink"><?= html_escape($kd->level_nama) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Cabang</dt>
					<dd class="text-ink"><?= html_escape($kd->cabang_nama) ?> (<?= html_escape($kd->cabang_wilayah) ?>)</dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Guru Pengampu</dt>
					<dd class="text-ink"><?= html_escape($kd->guru_nama) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Jadwal</dt>
					<dd class="text-ink"><?= html_escape(jadwal_tampil($kd)) ?></dd></div>
				<?php if ($isBilal): ?>
					<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Tipe Peserta</dt>
						<dd class="text-ink"><?= html_escape(ucfirst($kd->tipe_peserta)) ?>
							<?php if ($kd->nama_instansi !== NULL && $kd->nama_instansi !== ''): ?>
								— <?= html_escape($kd->nama_instansi) ?>
							<?php endif; ?>
						</dd></div>
				<?php endif; ?>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Biaya Pendaftaran</dt>
					<dd class="font-semibold text-ink"><?= rupiah($kd->biaya_pendaftaran) ?></dd></div>
				<div class="flex gap-3 py-2.5"><dt class="w-40 shrink-0 text-muted">Biaya SPP / bulan</dt>
					<dd class="font-semibold text-ink"><?= rupiah($kd->biaya_spp) ?></dd></div>
			</dl>
		</div>

		<?php if ($kd->mode === 'online'): ?>
			<div class="card p-6">
				<h2 class="mb-2 font-semibold text-ink">Link Kelas Online</h2>
				<?php if ($bolehLihatLink && $kd->link_meeting): ?>
					<?php if (filter_var($kd->link_meeting, FILTER_VALIDATE_URL)): ?>
						<a href="<?= html_escape($kd->link_meeting) ?>" target="_blank" rel="noopener"
							class="inline-block rounded-full bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-sky-700"><?= html_escape($kd->link_meeting) ?></a>
					<?php else: ?>
						<p class="text-sm text-ink"><?= html_escape($kd->link_meeting) ?></p>
					<?php endif; ?>
					<p class="mt-2 text-xs text-muted">Link ini khusus untuk peserta terdaftar dan guru pengampu — mohon dibagikan hanya seperlunya.</p>
				<?php else: ?>
					<p class="text-sm text-muted">Link meeting hanya ditampilkan untuk peserta yang terdaftar pada kelas ini (setelah pendaftaran aktif).</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="space-y-5">
		<div class="card p-6">
			<h2 class="mb-3 font-semibold text-ink">Pendaftaran</h2>

			<?php if (empty($user)): ?>
				<p class="mb-3 text-sm text-muted">Daftar sekarang — cukup isi registrasi singkat, akun peserta/wali dibuat otomatis.</p>
				<a href="<?= site_url('daftar/registrasi/'.$kd->id) ?>" class="btn-primary w-full">Daftar Kelas Ini</a>
				<p class="mt-4 text-center text-sm text-muted">
					Sudah punya akun? <a href="<?= site_url('auth/login') ?>" class="font-semibold text-primary hover:underline">Login</a>
				</p>

			<?php elseif ($user['role'] === 'peserta'): ?>
				<?php if ($pendaftaranSaya): ?>
					<p class="mb-3"><?= badge('pendaftaran', $pendaftaranSaya->status) ?></p>
					<?php if ($pendaftaranSaya->status === 'menunggu' && $kd->biaya_pendaftaran > 0): ?>
						<p class="mb-3 text-sm text-muted">Bukti pembayaran Anda sedang menunggu verifikasi admin. Pastikan bukti sudah diunggah di halaman Pendaftaran.</p>
					<?php elseif ($pendaftaranSaya->status === 'menunggu'): ?>
						<p class="mb-3 text-sm text-muted">Pendaftaran Anda sedang menunggu verifikasi admin.</p>
					<?php elseif ($pendaftaranSaya->status === 'aktif'): ?>
						<p class="mb-3 text-sm text-muted">Anda sudah terdaftar pada kelas ini. Jadwal dan link kelas ada di dashboard Anda.</p>
					<?php endif; ?>
					<a href="<?= site_url('peserta/pendaftaran') ?>" class="btn-outline w-full">Lihat Pendaftaran Saya</a>

				<?php elseif (empty($profilList)): ?>
					<p class="mb-3 text-sm text-muted">Lengkapi data peserta (Anda/anak) sebelum mendaftar kelas.</p>
					<a href="<?= site_url('peserta/profil/tambah') ?>" class="btn-ghost w-full">Tambah Data Peserta</a>

				<?php else: ?>
					<p class="mb-1 text-sm text-muted">Biaya pendaftaran: <strong class="text-ink"><?= rupiah($kd->biaya_pendaftaran) ?></strong></p>
					<p class="mb-3 text-xs text-muted"><?= $kd->biaya_pendaftaran > 0 ? 'Anda akan memilih peserta lalu boleh langsung mengunggah bukti.' : 'Tanpa biaya pendaftaran — pendaftaran langsung aktif.' ?></p>
					<a href="<?= site_url('daftar/registrasi/'.$kd->id) ?>" class="btn-primary w-full">
						<?= $kd->biaya_pendaftaran > 0 ? 'Daftar &amp; Lanjut Pembayaran' : 'Daftar Kelas' ?>
					</a>
				<?php endif; ?>

			<?php else: ?>
				<p class="text-sm text-muted">Anda login sebagai <?= html_escape($user['role']) ?> — pendaftaran dilakukan oleh akun peserta/wali.</p>
			<?php endif; ?>
		</div>

		<a href="<?= site_url('katalog') ?>" class="block text-center text-sm font-semibold text-primary hover:underline">&larr; Kembali ke Katalog</a>
	</div>
</div>
