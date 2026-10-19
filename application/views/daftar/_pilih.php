<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pemilih dua arah: Cabang → Program atau Program → Cabang.
 * Dipakai beranda & halaman /daftar.
 *
 * Variabel yang diharapkan: $programs, $cabangs, $rekap, $aksi (URL tujuan GET).
 * $rekap[program_id]['kelas'] = daftar kelas aktif (lihat Kelas_model::rekapProgram())
 * dipakai kartu program untuk menampilkan Level · jadwal · cabang.
 */

// Peta program_id => [cabang_id, ...] untuk penyaringan sisi klien.
$petaProgram = array();
foreach ($rekap as $pid => $info) {
	$petaProgram[(int) $pid] = array_map('intval', array_keys($info['cabang']));
}

// Daftar kelas per program (render awal & pemulihan tanpa filter cabang)
// dan per (cabang, program) untuk pembaruan saat cabang dipilih.
$kelasProgram = array();
$kelasCabang = array();
foreach ($rekap as $pid => $info) {
	$pid = (int) $pid;
	if (empty($info['kelas'])) {
		continue;
	}
	$kelasProgram[$pid] = $info['kelas'];
	foreach ($info['kelas'] as $k) {
		$kelasCabang[(int) $k['cabang_id']][$pid][] = $k;
	}
}

// Cabang dikelompokkan per wilayah.
$perWilayah = array();
foreach ($cabangs as $c) {
	$perWilayah[$c->wilayah][] = $c;
}

$maksBaris = 3;
$petaJson = json_encode($petaProgram);
$kelasJson = json_encode(
	array('perProgram' => $kelasProgram, 'perCabang' => $kelasCabang),
	JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
);
?>
<div class="card p-5 sm:p-6">
	<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
		<h2 class="text-lg font-bold text-ink">Mulai dari mana?</h2>
		<div class="inline-flex overflow-hidden rounded-full border border-line text-sm" role="tablist">
			<button type="button" class="js-tab px-4 py-1.5 bg-emerald-600 text-white font-semibold" data-target="panel-cabang">Pilih Cabang</button>
			<button type="button" class="js-tab px-4 py-1.5 bg-white text-slate-600 font-semibold" data-target="panel-program">Pilih Program</button>
		</div>
	</div>

	<!-- ALUR A: Cabang → Program -->
	<div id="panel-cabang" class="js-panel"
		data-utama="#grup-cabang-a" data-arah="program-dari-cabang" data-peta='<?= html_escape($petaJson) ?>'>
		<?= form_open($aksi, array('method' => 'get', 'class' => 'space-y-4')) ?>
			<div>
				<span class="block text-sm font-semibold text-ink mb-2">1. Pilih Cabang</span>
				<div id="grup-cabang-a" class="space-y-3">
					<?php foreach ($perWilayah as $wilayah => $daftar): ?>
						<div>
							<div class="mb-1 text-xs font-semibold uppercase tracking-wide text-muted"><?= html_escape($wilayah) ?></div>
							<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
								<?php foreach ($daftar as $c): ?>
									<?= $this->load->view('daftar/_kartu_cabang', array('c' => $c), TRUE) ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div>
				<span class="block text-sm font-semibold text-ink mb-2">2. Pilih Program</span>
				<div id="grup-program-a" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
					<?php foreach ($programs as $p): ?>
						<?= $this->load->view('daftar/_kartu_program', array('p' => $p, 'rekap' => $rekap, 'maksBaris' => $maksBaris), TRUE) ?>
					<?php endforeach; ?>
				</div>
				<p class="js-kosong hidden text-sm text-amber-700 mt-2">Belum ada program yang tersedia di cabang ini.</p>
			</div>
			<div class="flex flex-wrap items-end gap-3">
				<div>
					<label class="block text-sm font-medium text-slate-700 mb-1" for="mode-a">Mode</label>
					<select id="mode-a" name="mode" class="input w-auto">
						<option value="">Online &amp; Offline</option>
						<option value="online">Online</option>
						<option value="offline">Offline</option>
					</select>
				</div>
				<button type="submit" class="btn-primary">Lihat Kelas</button>
			</div>
		<?= form_close() ?>
	</div>

	<!-- ALUR B: Program → Cabang -->
	<div id="panel-program" class="js-panel hidden"
		data-utama="#grup-program-b" data-arah="cabang-dari-program" data-peta='<?= html_escape($petaJson) ?>'>
		<?= form_open($aksi, array('method' => 'get', 'class' => 'space-y-4')) ?>
			<div>
				<span class="block text-sm font-semibold text-ink mb-2">1. Pilih Program</span>
				<div id="grup-program-b" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
					<?php foreach ($programs as $p): ?>
						<?= $this->load->view('daftar/_kartu_program', array('p' => $p, 'rekap' => $rekap, 'maksBaris' => $maksBaris), TRUE) ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div>
				<span class="block text-sm font-semibold text-ink mb-2">2. Pilih Cabang</span>
				<div id="grup-cabang-b" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
					<?php foreach ($cabangs as $c): ?>
						<?= $this->load->view('daftar/_kartu_cabang', array('c' => $c), TRUE) ?>
					<?php endforeach; ?>
				</div>
				<p class="js-kosong hidden text-sm text-amber-700 mt-2">Belum ada cabang yang menyediakan program ini.</p>
			</div>
			<div class="flex flex-wrap items-end gap-3">
				<div>
					<label class="block text-sm font-medium text-slate-700 mb-1" for="mode-b">Mode</label>
					<select id="mode-b" name="mode" class="input w-auto">
						<option value="">Online &amp; Offline</option>
						<option value="online">Online</option>
						<option value="offline">Offline</option>
					</select>
				</div>
				<button type="submit" class="btn-primary">Lihat Kelas</button>
			</div>
		<?= form_close() ?>
	</div>

	<p class="mt-4 text-xs text-muted">Level tidak dipilih di sini — level peserta ditetapkan pengelola lewat proses <strong>penempatan</strong> setelah Anda mendaftar.</p>
</div>

<script type="application/json" id="data-pilih"><?= $kelasJson ?></script>
<script>
(function () {
	var data = { perProgram: {}, perCabang: {} };
	var el = document.getElementById('data-pilih');
	if (el) { try { data = JSON.parse(el.textContent); } catch (e) { data = { perProgram: {}, perCabang: {} }; } }

	function esc(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	}
	function teksBaris(k) {
		var bagian = [];
		if (k.level) { bagian.push(k.level); }
		var jadwal = k.jadwal ? String(k.jadwal) : '';
		var zona = k.zona ? String(k.zona) : '';
		if (jadwal && zona && jadwal.toLowerCase().indexOf(zona.toLowerCase()) === -1) { jadwal += ' ' + zona; }
		if (jadwal) { bagian.push(jadwal); }
		if (k.cabang) { bagian.push(k.cabang); }
		return bagian.join(' \u00b7 ');
	}
	function chipMode(mode) {
		if (mode === 'online') { return '<span class="shrink-0 inline-block text-[10px] font-medium rounded px-1.5 py-0.5 bg-sky-100 text-sky-800">Online</span>'; }
		if (mode === 'offline') { return '<span class="shrink-0 inline-block text-[10px] font-medium rounded px-1.5 py-0.5 bg-violet-100 text-violet-800">Offline</span>'; }
		return '';
	}
	function renderKelas(list, maks) {
		if (! list || ! list.length) {
			return '<li class="text-xs text-slate-400">Belum ada kelas di cabang ini.</li>';
		}
		var n = Math.min(list.length, maks);
		var html = '';
		for (var i = 0; i < n; i++) {
			html += '<li class="flex items-start justify-between gap-2">'
				+ '<span class="text-xs text-slate-600 leading-snug">' + esc(teksBaris(list[i])) + '</span>'
				+ chipMode(list[i].mode) + '</li>';
		}
		if (list.length > n) {
			html += '<li class="text-xs text-slate-400">+' + (list.length - n) + ' kelas lainnya</li>';
		}
		return html;
	}

	document.querySelectorAll('.js-tab').forEach(function (tab) {
		tab.addEventListener('click', function () {
			document.querySelectorAll('.js-tab').forEach(function (t) {
				t.classList.remove('bg-emerald-600', 'text-white');
				t.classList.add('bg-white', 'text-slate-600');
			});
			tab.classList.remove('bg-white', 'text-slate-600');
			tab.classList.add('bg-emerald-600', 'text-white');

			document.querySelectorAll('.js-panel').forEach(function (p) { p.classList.add('hidden'); });
			var target = document.getElementById(tab.dataset.target);
			if (target) { target.classList.remove('hidden'); }
		});
	});

	document.querySelectorAll('.js-panel').forEach(function (panel) {
		var utama = panel.querySelector(panel.dataset.utama);
		var arah = panel.dataset.arah;
		var opsi = panel.querySelectorAll('[data-opsi-item]');
		var kosong = panel.querySelector('.js-kosong');
		if (! utama || ! opsi.length) { return; }

		var peta = {};
		try { peta = JSON.parse(panel.dataset.peta || '{}'); } catch (e) { peta = {}; }

		function nilaiUtama() {
			var inp = utama.querySelector('input:checked');
			return inp ? inp.value : '';
		}

		// Hanya item "sekunder" yang difilter; item utama (mis. kartu cabang yang
		// sedang dipilih di alur Cabang→Program) selalu tampil.
		function sekunder(item) {
			return arah === 'program-dari-cabang'
				? item.dataset.program !== undefined
				: item.dataset.cabang !== undefined;
		}

		function cocok(item, nilai) {
			if (nilai === '') { return true; }
			if (arah === 'program-dari-cabang') {
				return (peta[item.dataset.program] || []).map(String).indexOf(String(nilai)) !== -1;
			}
			return (peta[nilai] || []).map(String).indexOf(String(item.dataset.cabang)) !== -1;
		}

		function perbaruiKelas(cabang) {
			panel.querySelectorAll('[data-kelas-list]').forEach(function (box) {
				var kartu = box.closest('[data-program]');
				if (! kartu) { return; }
				var pid = kartu.dataset.program;
				var list = cabang === ''
					? (data.perProgram[pid] || [])
					: ((data.perCabang[cabang] || {})[pid] || []);
				box.innerHTML = renderKelas(list, parseInt(box.dataset.maks, 10) || 3);
			});
		}

		function saring() {
			var nilai = nilaiUtama();
			var tampil = 0;
			opsi.forEach(function (item) {
				if (! sekunder(item)) {
					item.classList.remove('hidden');
					return;
				}
				var ok = cocok(item, nilai);
				item.classList.toggle('hidden', ! ok);
				if (! ok) {
					var input = item.querySelector('input');
					if (input && input.checked) { input.checked = false; }
				}
				if (ok) { tampil++; }
			});
			if (kosong) { kosong.classList.toggle('hidden', tampil > 0); }
			if (arah === 'program-dari-cabang') { perbaruiKelas(nilai); }
		}

		utama.addEventListener('change', saring);
		saring();
	});
})();
</script>
