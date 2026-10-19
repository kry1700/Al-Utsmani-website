<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan_model extends CI_Model {

	/**
	 * Daftar tagihan SPP (join peserta).
	 *
	 * @param string $periode 'YYYY-MM'
	 * @param string $status  '' | 'belum' | 'lunas' | 'tunggakan' (belum + lewat tempo)
	 */
	public function daftar($periode = '', $status = '')
	{
		$this->db
			->select('t.*, ps.nama AS peserta_nama, ps.no_wa AS peserta_no_wa', FALSE)
			->from('tagihan_spp t')
			->join('peserta ps', 'ps.id = t.peserta_id');

		if ($periode !== '') {
			$this->db->where('t.periode', $periode);
		}
		if ($status === 'tunggakan') {
			$this->db->where('t.status', 'belum')->where('t.jatuh_tempo <', date('Y-m-d'));
		} elseif ($status !== '') {
			$this->db->where('t.status', $status);
		}

		return $this->db
			->order_by('t.periode', 'DESC')
			->order_by('ps.nama', 'ASC')
			->get()
			->result();
	}

	public function find($id)
	{
		return $this->db
			->select('t.*, ps.nama AS peserta_nama', FALSE)
			->from('tagihan_spp t')
			->join('peserta ps', 'ps.id = t.peserta_id')
			->where('t.id', (int) $id)
			->get()
			->row();
	}

	/** Tagihan milik satu peserta (untuk halaman SPP peserta), terbaru di atas. */
	public function byPeserta($pesertaId)
	{
		return $this->db
			->where('peserta_id', (int) $pesertaId)
			->order_by('periode', 'DESC')
			->get('tagihan_spp')
			->result();
	}

	/** Semua tagihan milik SATU akun users (lintas profil peserta), terbaru di atas. */
	public function byUser($userId)
	{
		return $this->db
			->select('t.*, ps.nama AS peserta_nama', FALSE)
			->from('tagihan_spp t')
			->join('peserta ps', 'ps.id = t.peserta_id')
			->where('ps.user_id', (int) $userId)
			->order_by('t.periode', 'DESC')
			->order_by('t.id', 'DESC')
			->get()
			->result();
	}

	/**
	 * Ringkasan tagihan belum lunas untuk beberapa profil peserta
	 * (kartu dashboard) → {jumlah, total}.
	 */
	public function belumLunasUntuk(array $pesertaIds)
	{
		if (! $pesertaIds) {
			return (object) array('jumlah' => 0, 'total' => 0);
		}
		$row = $this->db
			->select('COUNT(*) AS jumlah, COALESCE(SUM(nominal), 0) AS total', FALSE)
			->where_in('peserta_id', $pesertaIds)
			->where('status', 'belum')
			->get('tagihan_spp')
			->row();
		return (object) array('jumlah' => (int) $row->jumlah, 'total' => (int) $row->total);
	}

	/**
	 * Ringkasan tagihan satu periode → {total, lunas, belum, tunggakan_jumlah,
	 * tunggakan_total}. Satu query agregat (bukan per baris).
	 */
	public function ringkasan($periode)
	{
		$row = $this->db
			->select("COUNT(*) AS total,
				COALESCE(SUM(status = 'lunas'), 0) AS lunas,
				COALESCE(SUM(status = 'belum'), 0) AS belum,
				COALESCE(SUM(CASE WHEN status = 'belum' AND jatuh_tempo < ".$this->db->escape(date('Y-m-d'))." THEN 1 ELSE 0 END), 0) AS tunggakan_jumlah,
				COALESCE(SUM(CASE WHEN status = 'belum' AND jatuh_tempo < ".$this->db->escape(date('Y-m-d'))." THEN nominal ELSE 0 END), 0) AS tunggakan_total", FALSE)
			->where('periode', $periode)
			->get('tagihan_spp')
			->row();

		return (object) array(
			'total' => (int) $row->total,
			'lunas' => (int) $row->lunas,
			'belum' => (int) $row->belum,
			'tunggakan_jumlah' => (int) $row->tunggakan_jumlah,
			'tunggakan_total' => (int) $row->tunggakan_total,
		);
	}

	public function isDuplicate($pesertaId, $periode)
	{
		return $this->db
			->where('peserta_id', (int) $pesertaId)
			->where('periode', $periode)
			->count_all_results('tagihan_spp') > 0;
	}

	/**
	 * Peserta dengan pendaftaran aktif yang BELUM ditagih pada periode tertentu.
	 * 4 query tetap (bukan N+1): daftar peserta aktif, tagihan periode,
	 * selisih di PHP, lalu data peserta + kelas aktifnya.
	 *
	 * @param string $periode 'YYYY-MM'
	 * @return array Setiap item: peserta (objek), nominal_hint (int), daftar_kelas (string)
	 */
	public function belumDitagih($periode)
	{
		// 1. Peserta yang punya minimal satu pendaftaran aktif.
		$aktif = $this->db->select('DISTINCT peserta_id', FALSE)
			->where('status', 'aktif')
			->get('pendaftaran')->result();
		$idsAktif = array();
		foreach ($aktif as $row) {
			$idsAktif[] = (int) $row->peserta_id;
		}
		if (! $idsAktif) {
			return array();
		}

		// 2. Peserta yang sudah punya tagihan di periode ini.
		$sudah = $this->db->select('peserta_id')
			->where('periode', $periode)
			->get('tagihan_spp')->result();
		$idsSudah = array();
		foreach ($sudah as $row) {
			$idsSudah[] = (int) $row->peserta_id;
		}

		$ids = array_values(array_diff($idsAktif, $idsSudah));
		if (! $ids) {
			return array();
		}

		// 3. Data pesertanya.
		$pesertaRows = $this->db->select('id, nama, no_wa')
			->where_in('id', $ids)->order_by('nama', 'ASC')
			->get('peserta')->result();

		// 4. Semua kelas aktifnya (untuk hint nominal dari pendaftaran terlama + label).
		$kelasRows = $this->db
			->select('pd.peserta_id, pd.id AS pendaftaran_id, k.biaya_spp,
				p.kode AS program_kode, l.nama AS level_nama, c.nama AS cabang_nama, k.mode', FALSE)
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('pd.status', 'aktif')
			->where_in('pd.peserta_id', $ids)
			->order_by('pd.id', 'ASC') // pendaftaran terlama dulu → jadi dasar hint
			->get()
			->result();

		$perPeserta = array();
		foreach ($kelasRows as $row) {
			$pid = (int) $row->peserta_id;
			if (! isset($perPeserta[$pid])) {
				$perPeserta[$pid] = array('hint' => (int) $row->biaya_spp, 'label' => array());
			}
			$perPeserta[$pid]['label'][] = label_kelas(array(
				'program_kode' => $row->program_kode,
				'level_nama' => $row->level_nama,
				'cabang_nama' => $row->cabang_nama,
				'mode' => $row->mode,
			));
		}

		$out = array();
		foreach ($pesertaRows as $ps) {
			$pid = (int) $ps->id;
			if (! isset($perPeserta[$pid])) {
				continue; // tidak ada kelas aktif — dilewati (tidak mungkin terjadi)
			}
			$out[] = array(
				'peserta' => $ps,
				'nominal_hint' => $perPeserta[$pid]['hint'],
				'daftar_kelas' => implode(', ', $perPeserta[$pid]['label']),
			);
		}
		return $out;
	}

	/**
	 * Buat banyak tagihan untuk satu periode dalam transaksi.
	 * Peserta yang sudah punya tagihan periode itu dilewati (anti-duplikat).
	 *
	 * @param array $items [{peserta_id, nominal}, ...]
	 * @return array [dibuat => int, dilewati => int]
	 */
	public function createBatch($periode, $jatuhTempo, array $items)
	{
		$result = array('dibuat' => 0, 'dilewati' => 0);
		if (! $items) {
			return $result;
		}

		$pesertaIds = array();
		foreach ($items as $item) {
			$pesertaIds[] = (int) $item['peserta_id'];
		}
		$pesertaIds = array_values(array_unique($pesertaIds));

		$this->db->trans_begin();

		// Cek ulang di dalam transaksi agar aman terhadap pembukaan ganda.
		$sudah = $this->db->select('peserta_id')
			->where('periode', $periode)
			->where_in('peserta_id', $pesertaIds)
			->get('tagihan_spp')->result();
		$setSudah = array();
		foreach ($sudah as $row) {
			$setSudah[] = (int) $row->peserta_id;
		}

		$rows = array();
		foreach ($items as $item) {
			$pid = (int) $item['peserta_id'];
			if (in_array($pid, $setSudah, TRUE)) {
				$result['dilewati']++;
				continue;
			}
			$rows[] = array(
				'peserta_id' => $pid,
				'periode' => $periode,
				'nominal' => (int) $item['nominal'],
				'jatuh_tempo' => $jatuhTempo,
			);
		}

		if ($rows) {
			$this->db->insert_batch('tagihan_spp', $rows);
			$result['dibuat'] = count($rows);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('dibuat' => 0, 'dilewati' => 0);
		}

		$this->db->trans_commit();
		return $result;
	}
}
