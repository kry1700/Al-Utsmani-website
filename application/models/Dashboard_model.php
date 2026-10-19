<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Agregasi dashboard admin & rekap per cabang.
 * Semua metrik dihitung dengan 1 query per angka (bukan per baris/ cabang)
 * agar tidak terjadi N+1 pada halaman daftar/dashboard.
 *
 * Definisi bisnis:
 *   - peserta aktif = punya >= 1 pendaftaran berstatus aktif
 *   - peserta baru  = peserta aktif yang pendaftarannya pertama jatuh pada periode
 *   - pemasukan     = pembayaran terverifikasi, dihitung dari tanggal verifikasi;
 *                     cabang mengikuti kelas. SPP memakai cabang pendaftaran aktif
 *                     PERTAMA (terlama) peserta agar tidak dihitung ganda.
 *   - tunggakan     = tagihan belum lunas + lewat jatuh tempo (saat ini)
 */
class Dashboard_model extends CI_Model {

	/**
	 * Subquery pendaftaran aktif PERTAMA (terlama) tiap peserta → cabangnya.
	 * Dipakai atribusi pembayaran SPP ke cabang.
	 */
	private function pendaftaranPertamaSql()
	{
		return $this->db
			->select('peserta_id, MIN(id) AS ref_id')
			->from('pendaftaran')
			->where('status', 'aktif')
			->group_by('peserta_id')
			->get_compiled_select();
	}

	/** Rentang datetime pemasukan dari periode 'YYYY-MM' (akhir exclusive). */
	private function rentang($periode)
	{
		return rentang_periode($periode);
	}

	/**
	 * Peserta baru / lama / total pada satu periode.
	 *
	 * @param string   $periode  'YYYY-MM'
	 * @param int|null $cabangId bila diisi, hanya peserta aktif di cabang itu
	 * @return object {total, baru, lama}
	 */
	public function pesertaBaruLama($periode, $cabangId = NULL)
	{
		list($mulai, $akhir) = $this->rentang($periode);
		$mulaiTanggal = substr($mulai, 0, 10);
		$akhirTanggal = substr($akhir, 0, 10);

		// Subquery: peserta aktif (opsional terikat satu cabang).
		$aktif = $this->db
			->select('DISTINCT pd.peserta_id', FALSE)
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('pd.status', 'aktif');
		if ($cabangId !== NULL) {
			$aktif->where('k.cabang_id', (int) $cabangId);
		}
		$aktifSql = $aktif->get_compiled_select();

		// Subquery: pendaftaran pertama tiap peserta (seluruh riwayat, lintas cabang).
		$firstSql = $this->db
			->select('peserta_id, MIN(created_at) AS pertama')
			->from('pendaftaran')
			->group_by('peserta_id')
			->get_compiled_select();

		// Derived table + CASE agregat (SQL agregasi, escape via QB).
		$sql = 'SELECT COUNT(*) AS total,
			SUM(CASE WHEN DATE(f.pertama) >= '.$this->db->escape($mulaiTanggal)
				.' AND DATE(f.pertama) < '.$this->db->escape($akhirTanggal).' THEN 1 ELSE 0 END) AS baru,
			SUM(CASE WHEN DATE(f.pertama) < '.$this->db->escape($mulaiTanggal).' THEN 1 ELSE 0 END) AS lama
			FROM ('.$aktifSql.') a
			JOIN ('.$firstSql.') f ON f.peserta_id = a.peserta_id';

		$row = $this->db->query($sql)->row();
		return (object) array(
			'total' => (int) $row->total,
			'baru' => (int) $row->baru,
			'lama' => (int) $row->lama,
		);
	}

	/**
	 * @param int|null $cabangId NULL = seluruh guru aktif; terisi = guru aktif
	 *                           yang mengampu kelas aktif di cabang itu
	 */
	public function guruAktif($cabangId = NULL)
	{
		if ($cabangId === NULL) {
			return (int) $this->db->where('status', 'aktif')->count_all_results('guru');
		}

		$row = $this->db
			->select('COUNT(DISTINCT k.guru_id) AS jumlah', FALSE)
			->from('kelas k')
			->join('guru g', 'g.id = k.guru_id')
			->where('k.status', 'aktif')
			->where('g.status', 'aktif')
			->where('k.cabang_id', (int) $cabangId)
			->get()
			->row();
		return (int) $row->jumlah;
	}

	/** @param int|null $cabangId filter cabang opsional. */
	public function kelasAktif($cabangId = NULL)
	{
		$this->db->where('status', 'aktif');
		if ($cabangId !== NULL) {
			$this->db->where('cabang_id', (int) $cabangId);
		}
		return (int) $this->db->count_all_results('kelas');
	}

	public function cabangAktif()
	{
		return (int) $this->db->where('status', 'aktif')->count_all_results('cabang');
	}

	/**
	 * Total pemasukan terverifikasi pada periode.
	 *
	 * @param string   $periode  'YYYY-MM'
	 * @param int|null $cabangId bila diisi, hanya pembayaran yang cabangnya cabang itu
	 */
	public function pemasukan($periode, $cabangId = NULL)
	{
		list($mulai, $akhir) = $this->rentang($periode);

		$dasar = function ($jenis) use ($mulai, $akhir) {
			// closure kecil agar filter periode/status identik di kedua cabang query
			return $this->db
				->where('b.status', 'diverifikasi')
				->where('b.jenis', $jenis)
				->where('b.verified_at >=', $mulai)
				->where('b.verified_at <', $akhir);
		};

		if ($cabangId === NULL) {
			$row = $dasar('pendaftaran')
				->select('COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
				->from('pembayaran b')
				->get()
				->row();
			$totalPendaftaran = (int) $row->total;

			$row = $dasar('spp')
				->select('COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
				->from('pembayaran b')
				->get()
				->row();
			return $totalPendaftaran + (int) $row->total;
		}

		// Pendaftaran: cabang langsung dari kelas pendaftaran.
		$row = $dasar('pendaftaran')
			->select('COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
			->from('pembayaran b')
			->join('pendaftaran pd', 'pd.id = b.pendaftaran_id')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('k.cabang_id', (int) $cabangId)
			->get()
			->row();
		$totalPendaftaran = (int) $row->total;

		// SPP: cabang dari pendaftaran aktif pertama peserta.
		$sub = $this->pendaftaranPertamaSql();
		$row = $dasar('spp')
			->select('COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
			->from('pembayaran b')
			->join('tagihan_spp t', 't.id = b.tagihan_spp_id')
			->join('peserta ps', 'ps.id = t.peserta_id')
			->join('('.$sub.') fp', 'fp.peserta_id = ps.id', '', FALSE)
			->join('kelas k', 'k.id = fp.ref_id')
			->where('k.cabang_id', (int) $cabangId)
			->get()
			->row();

		return $totalPendaftaran + (int) $row->total;
	}

	/**
	 * Pemasukan per cabang pada periode → [cabang_id => nominal].
	 * (Angka total di kartu memakai pemasukan() terpisah; atribusi SPP sama.)
	 */
	public function pemasukanPerCabang($periode)
	{
		list($mulai, $akhir) = $this->rentang($periode);
		$out = array();

		$dasar = function ($jenis) use ($mulai, $akhir) {
			return $this->db
				->where('b.status', 'diverifikasi')
				->where('b.jenis', $jenis)
				->where('b.verified_at >=', $mulai)
				->where('b.verified_at <', $akhir);
		};

		$rows = $dasar('pendaftaran')
			->select('k.cabang_id, COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
			->from('pembayaran b')
			->join('pendaftaran pd', 'pd.id = b.pendaftaran_id')
			->join('kelas k', 'k.id = pd.kelas_id')
			->group_by('k.cabang_id')
			->get()
			->result();
		foreach ($rows as $row) {
			$out[(int) $row->cabang_id] = (int) $row->total;
		}

		$sub = $this->pendaftaranPertamaSql();
		$rows = $dasar('spp')
			->select('k.cabang_id, COALESCE(SUM(b.jumlah), 0) AS total', FALSE)
			->from('pembayaran b')
			->join('tagihan_spp t', 't.id = b.tagihan_spp_id')
			->join('peserta ps', 'ps.id = t.peserta_id')
			->join('('.$sub.') fp', 'fp.peserta_id = ps.id', '', FALSE)
			->join('kelas k', 'k.id = fp.ref_id')
			->group_by('k.cabang_id')
			->get()
			->result();
		foreach ($rows as $row) {
			$id = (int) $row->cabang_id;
			$out[$id] = isset($out[$id]) ? $out[$id] + (int) $row->total : (int) $row->total;
		}

		return $out;
	}

	/**
	 * Tunggakan keseluruhan → {jumlah, total}.
	 *
	 * @param int|null $cabangId bila diisi, hanya tagihan peserta yang aktif
	 *                           di cabang itu (bisa >1 cabang bila multi-kelas)
	 */
	public function tunggakan($cabangId = NULL)
	{
		if ($cabangId === NULL) {
			$row = $this->db
				->select('COUNT(*) AS jumlah, COALESCE(SUM(nominal), 0) AS total', FALSE)
				->where('status', 'belum')
				->where('jatuh_tempo <', date('Y-m-d'))
				->get('tagihan_spp')
				->row();
			return (object) array('jumlah' => (int) $row->jumlah, 'total' => (int) $row->total);
		}

		// DISTINCT dulu per tagihan agar baris multi-kelas tidak melipatgandakan nominal.
		$rows = $this->db
			->select('DISTINCT t.id AS tagihan_id, t.nominal', FALSE)
			->from('tagihan_spp t')
			->join('pendaftaran pd', 'pd.peserta_id = t.peserta_id')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('t.status', 'belum')
			->where('t.jatuh_tempo <', date('Y-m-d'))
			->where('pd.status', 'aktif')
			->where('k.cabang_id', (int) $cabangId)
			->get()
			->result();

		$total = 0;
		foreach ($rows as $row) {
			$total += (int) $row->nominal;
		}
		return (object) array('jumlah' => count($rows), 'total' => $total);
	}

	/** Tunggakan per cabang → [cabang_id => {jumlah, total}] (semua cabang aktif punya entri opsional). */
	public function tunggakanPerCabang()
	{
		$rows = $this->db
			->select('DISTINCT t.id AS tagihan_id, t.nominal, k.cabang_id', FALSE)
			->from('tagihan_spp t')
			->join('pendaftaran pd', 'pd.peserta_id = t.peserta_id')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('t.status', 'belum')
			->where('t.jatuh_tempo <', date('Y-m-d'))
			->where('pd.status', 'aktif')
			->get()
			->result();

		$out = array();
		foreach ($rows as $row) {
			$id = (int) $row->cabang_id;
			if (! isset($out[$id])) {
				$out[$id] = (object) array('jumlah' => 0, 'total' => 0);
			}
			$out[$id]->jumlah++;
			$out[$id]->total += (int) $row->nominal;
		}
		return $out;
	}

	/** Peserta aktif per cabang → [cabang_id => jumlah peserta]. */
	public function pesertaAktifPerCabang()
	{
		$rows = $this->db
			->select('k.cabang_id, COUNT(DISTINCT pd.peserta_id) AS jumlah', FALSE)
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('pd.status', 'aktif')
			->group_by('k.cabang_id')
			->get()
			->result();
		$out = array();
		foreach ($rows as $row) {
			$out[(int) $row->cabang_id] = (int) $row->jumlah;
		}
		return $out;
	}

	/** Jumlah program berbeda pada kelas aktif per cabang → [cabang_id => jumlah]. */
	public function programAktifPerCabang()
	{
		$rows = $this->db
			->select('cabang_id, COUNT(DISTINCT program_id) AS jumlah', FALSE)
			->from('kelas')
			->where('status', 'aktif')
			->group_by('cabang_id')
			->get()
			->result();
		$out = array();
		foreach ($rows as $row) {
			$out[(int) $row->cabang_id] = (int) $row->jumlah;
		}
		return $out;
	}

	/** Jumlah kelas aktif per cabang → [cabang_id => jumlah]. */
	public function kelasAktifPerCabang()
	{
		$rows = $this->db
			->select('cabang_id, COUNT(*) AS jumlah', FALSE)
			->from('kelas')
			->where('status', 'aktif')
			->group_by('cabang_id')
			->get()
			->result();
		$out = array();
		foreach ($rows as $row) {
			$out[(int) $row->cabang_id] = (int) $row->jumlah;
		}
		return $out;
	}

	/** Guru aktif yang mengampu kelas aktif per cabang → [cabang_id => jumlah]. */
	public function guruPerCabang()
	{
		$rows = $this->db
			->select('k.cabang_id, COUNT(DISTINCT k.guru_id) AS jumlah', FALSE)
			->from('kelas k')
			->join('guru g', 'g.id = k.guru_id')
			->where('k.status', 'aktif')
			->where('g.status', 'aktif')
			->group_by('k.cabang_id')
			->get()
			->result();
		$out = array();
		foreach ($rows as $row) {
			$out[(int) $row->cabang_id] = (int) $row->jumlah;
		}
		return $out;
	}

	/**
	 * Detail peserta aktif di satu cabang (untuk drill-down Perkembangan).
	 * Satu query untuk semua baris; dikelompokkan per peserta di PHP.
	 *
	 * @return array Setiap item: peserta (objek), sejak (tanggal), kelas (array label)
	 */
	public function pesertaAktifDetail($cabangId)
	{
		$rows = $this->db
			->select('ps.id, ps.nama, ps.no_wa, ps.kemampuan_awal,
				pd.created_at AS sejak,
				p.kode AS program_kode, l.nama AS level_nama, c.nama AS cabang_nama, k.mode', FALSE)
			->from('peserta ps')
			->join('pendaftaran pd', 'pd.peserta_id = ps.id')
			->join('kelas k', 'k.id = pd.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('pd.status', 'aktif')
			->where('k.cabang_id', (int) $cabangId)
			->order_by('ps.nama', 'ASC')
			->order_by('pd.id', 'ASC')
			->get()
			->result();

		$out = array();
		foreach ($rows as $row) {
			$pid = (int) $row->id;
			if (! isset($out[$pid])) {
				$out[$pid] = array(
					'peserta' => (object) array(
						'id' => $row->id,
						'nama' => $row->nama,
						'no_wa' => $row->no_wa,
						'kemampuan_awal' => $row->kemampuan_awal,
					),
					'sejak' => $row->sejak,
					'kelas' => array(),
				);
			}
			$out[$pid]['kelas'][] = label_kelas(array(
				'program_kode' => $row->program_kode,
				'level_nama' => $row->level_nama,
				'cabang_nama' => $row->cabang_nama,
				'mode' => $row->mode,
			));
		}
		return $out;
	}

	/**
	 * Statistik absensi + progres terakhir tiap peserta aktif di satu cabang.
	 * Dua query (kehadiran agregat + baris terbaru), dikelompokkan di PHP.
	 *
	 * @return array [peserta_id => {total, hadir, hal_mulai, hal_selesai,
	 *                               pertemuan_nomor, pertemuan_tanggal}]
	 */
	public function absensiDetail($cabangId)
	{
		$statistik = array();
		$rows = $this->db
			->select("a.peserta_id, COUNT(*) AS total,
				COALESCE(SUM(a.status = 'hadir'), 0) AS hadir", FALSE)
			->from('absensi a')
			->join('pertemuan pm', 'pm.id = a.pertemuan_id')
			->join('kelas k', 'k.id = pm.kelas_id')
			->where('k.cabang_id', (int) $cabangId)
			->group_by('a.peserta_id')
			->get()
			->result();
		foreach ($rows as $row) {
			$statistik[(int) $row->peserta_id] = (object) array(
				'total' => (int) $row->total,
				'hadir' => (int) $row->hadir,
				'hal_mulai' => NULL,
				'hal_selesai' => NULL,
				'pertemuan_nomor' => NULL,
				'pertemuan_tanggal' => NULL,
			);
		}

		// Baris absensi terbaru per peserta (urut tanggal desc → yang pertama menang).
		$rows = $this->db
			->select('a.peserta_id, a.hal_mulai, a.hal_selesai,
				pm.nomor AS pertemuan_nomor, pm.tanggal AS pertemuan_tanggal', FALSE)
			->from('absensi a')
			->join('pertemuan pm', 'pm.id = a.pertemuan_id')
			->join('kelas k', 'k.id = pm.kelas_id')
			->where('k.cabang_id', (int) $cabangId)
			->order_by('pm.tanggal', 'DESC')
			->order_by('pm.nomor', 'DESC')
			->order_by('a.id', 'DESC')
			->get()
			->result();
		foreach ($rows as $row) {
			$pid = (int) $row->peserta_id;
			if (isset($statistik[$pid])) {
				$statistik[$pid]->hal_mulai = $row->hal_mulai;
				$statistik[$pid]->hal_selesai = $row->hal_selesai;
				$statistik[$pid]->pertemuan_nomor = $row->pertemuan_nomor;
				$statistik[$pid]->pertemuan_tanggal = $row->pertemuan_tanggal;
			}
		}
		return $statistik;
	}
}
