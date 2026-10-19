<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mb-6">
	<h1 class="page-title">Dashboard Admin</h1>
	<p class="mt-1 text-sm text-muted">
		Ringkasan <strong class="font-semibold text-ink"><?= html_escape(nama_bulan($periode)) ?></strong>
		<?= $cabangId ? '— cabang terpilih' : '— seluruh cabang' ?>.
		Pemasukan dihitung dari tanggal verifikasi pembayaran.
	</p>
</div>

<form method="get" action="<?= site_url('admin/dashboard') ?>" class="card mb-6 flex flex-wrap items-end gap-3 p-5">
	<div>
		<label class="label" for="periode">Periode</label>
		<input type="month" id="periode" name="periode" value="<?= html_escape($periode) ?>" class="input w-auto">
	</div>
	<div>
		<label class="label" for="cabang">Cabang</label>
		<select id="cabang" name="cabang" class="input w-auto">
			<option value="">Semua Cabang</option>
			<?php foreach ($cabangList as $c): ?>
				<option value="<?= (int) $c->id ?>" <?= (int) $cabangId === (int) $c->id ? 'selected' : '' ?>>
					<?= html_escape($c->nama) ?> (<?= html_escape($c->wilayah) ?>)
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<button type="submit" class="btn-primary">Tampilkan</button>
	<a href="<?= site_url('admin/dashboard') ?>" class="btn-ghost">Reset</a>
</form>

<section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Peserta Aktif</div>
				<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $peserta->total ?></div>
				<div class="mt-1 text-xs text-muted">Baru: <?= (int) $peserta->baru ?> &middot; Lama: <?= (int) $peserta->lama ?></div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
			</span>
		</div>
	</div>

	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Belum Ditempatkan</div>
				<div class="mt-1 text-2xl font-bold <?= $belumLevel > 0 ? 'text-amber-600' : 'text-primary' ?>"><?= (int) $belumLevel ?></div>
				<a href="<?= site_url('admin/penempatan?level=belum') ?>" class="mt-1 inline-block text-xs font-semibold text-primary hover:underline">Atur penempatan level &rarr;</a>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814"/></svg>
			</span>
		</div>
	</div>

	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Guru Aktif</div>
				<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $guruAktif ?></div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
			</span>
		</div>
	</div>

	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Kelas Aktif</div>
				<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $kelasAktif ?></div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
			</span>
		</div>
	</div>

	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Cabang Aktif</div>
				<div class="mt-1 text-2xl font-bold text-primary"><?= (int) $cabangAktif ?></div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
			</span>
		</div>
	</div>

	<div class="rounded-lg bg-primary p-5 text-white shadow-glow">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-white/70">Pemasukan Terverifikasi</div>
				<div class="mt-1 text-2xl font-bold"><?= rupiah($pemasukan) ?></div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
			</span>
		</div>
	</div>

	<div class="stat-card">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0">
				<div class="text-xs font-medium uppercase tracking-wide text-muted">Tunggakan</div>
				<div class="mt-1 text-2xl font-bold <?= $tunggakan->jumlah > 0 ? 'text-rose-600' : 'text-primary' ?>"><?= rupiah($tunggakan->total) ?></div>
				<div class="mt-1 text-xs text-muted"><?= (int) $tunggakan->jumlah ?> tagihan belum lunas</div>
			</div>
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
				<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
			</span>
		</div>
	</div>
</section>

<div class="grid gap-4 lg:grid-cols-2">
	<section class="card overflow-hidden">
		<div class="card-head">
			<h2 class="font-semibold text-ink">Pemasukan per Cabang — <?= html_escape(nama_bulan($periode)) ?></h2>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-line">
				<thead class="bg-slate-50">
					<tr>
						<th class="th">Cabang</th>
						<th class="th text-right">Pemasukan</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-line">
					<?php
					$adaIsi = FALSE;
					foreach ($namaCabang as $cid => $nama):
						$nominal = isset($pemasukanPerCabang[$cid]) ? (int) $pemasukanPerCabang[$cid] : 0;
						if ($nominal <= 0) { continue; }
						$adaIsi = TRUE;
					?>
						<tr class="hover:bg-canvas">
							<td class="td"><?= html_escape($nama) ?></td>
							<td class="td text-right font-medium"><?= rupiah($nominal) ?></td>
						</tr>
					<?php endforeach; ?>
					<?php if (! $adaIsi): ?>
						<tr><td colspan="2" class="td text-muted">Belum ada pemasukan terverifikasi pada periode ini.</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>

	<section class="card overflow-hidden">
		<div class="card-head">
			<h2 class="font-semibold text-ink">Pintasan</h2>
		</div>
		<ul class="divide-y divide-line">
			<?php
			$pintasan = array(
				array('label' => 'Verifikasi pembayaran menunggu', 'href' => site_url('admin/pembayaran'), 'ket' => 'Pembayaran'),
				array('label' => 'Tagihan SPP periode ini', 'href' => site_url('admin/tagihan?periode='.$periode), 'ket' => 'SPP'),
				array('label' => 'Buat tagihan SPP massal', 'href' => site_url('admin/tagihan/buat?periode='.$periode), 'ket' => 'SPP'),
				array('label' => 'Perkembangan per cabang', 'href' => site_url('admin/perkembangan'), 'ket' => 'Rekap'),
				array('label' => 'Penempatan level peserta', 'href' => site_url('admin/penempatan'), 'ket' => 'Level'),
				array('label' => 'Tambah kelas baru', 'href' => site_url('admin/kelas/tambah'), 'ket' => 'Kelas'),
			);
			foreach ($pintasan as $p):
			?>
				<li>
					<a href="<?= $p['href'] ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-ink transition-colors hover:bg-canvas">
						<span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary">
							<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
						</span>
						<span class="flex-1"><?= html_escape($p['label']) ?></span>
						<span class="text-xs text-muted"><?= html_escape($p['ket']) ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
</div>
