<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
	<h1 class="page-title">Profil dan Data Peserta</h1>
	<a href="<?= site_url('peserta/profil/tambah') ?>" class="btn-primary">Tambah Peserta</a>
</div>

<p class="mb-5 text-sm text-muted">
	Satu akun dapat menaungi beberapa peserta (misalnya orang tua dengan beberapa anak).
	Data peserta tidak dihapus agar riwayat belajar tetap tersimpan.
</p>

<?php if (! $profilList): ?>
	<div class="card p-8 text-center text-sm text-muted">
		Belum ada data peserta pada akun ini. Klik <strong class="text-ink">Tambah Peserta</strong> untuk melengkapi.
	</div>
<?php else: ?>
	<div class="grid gap-4 sm:grid-cols-2">
		<?php foreach ($profilList as $p): ?>
			<div class="card p-5">
				<div class="mb-3 flex items-start justify-between gap-2">
					<div>
						<h2 class="font-semibold text-ink"><?= html_escape($p->nama) ?></h2>
						<div class="mt-1"><?= badge_level($p->level_nama, $p->level_urutan) ?></div>
					</div>
					<a href="<?= site_url('peserta/profil/edit/'.$p->id) ?>" class="text-sm font-semibold text-primary hover:underline">Edit</a>
				</div>
				<?php if ($p->level_id === NULL): ?>
					<p class="mb-2 text-xs text-amber-700">Level ditetapkan pengelola lewat proses penempatan.</p>
				<?php endif; ?>
				<dl class="space-y-1 text-sm text-muted">
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">Jenis Kelamin</dt>
						<dd><?= $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' ?></dd></div>
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">Tanggal Lahir</dt>
						<dd><?= html_escape(tanggal_indo($p->tanggal_lahir)) ?></dd></div>
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">No. WhatsApp</dt>
						<dd><?= html_escape($p->no_wa) ?></dd></div>
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">Nama Wali</dt>
						<dd><?= html_escape($p->nama_wali) ?></dd></div>
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">Kemampuan Awal</dt>
						<dd><?= $p->kemampuan_awal === 'hafalan' ? 'Sudah punya hafalan' : 'Pemula' ?></dd></div>
					<div class="flex gap-2"><dt class="w-32 shrink-0 text-muted">Alamat</dt>
						<dd><?= html_escape($p->alamat) ?></dd></div>
				</dl>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
