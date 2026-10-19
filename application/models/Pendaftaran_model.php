<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendaftaran_model extends CI_Model {

	/** Kolom + label kelas dalam satu query (untuk daftar pendaftaran peserta). */
	private function kolom()
	{
		return 'pd.*,
			k.mode, k.jadwal, k.zona_waktu, k.link_meeting,
			k.biaya_pendaftaran, k.biaya_spp, k.status AS kelas_status,
			p.kode AS program_kode, p.nama AS program_nama,
			l.nama AS level_nama,
			c.id AS cabang_id, c.nama AS cabang_nama, c.wilayah AS cabang_wilayah,
			g.nama AS guru_nama';
	}

	private function dasar()
	{
		return $this->db
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->join('guru g', 'g.id = k.guru_id');
	}

	/** Riwayat pendaftaran milik satu peserta (terbaru di atas). */
	public function byPeserta($pesertaId)
	{
		return $this->dasar()
			->select($this->kolom(), FALSE)
			->where('pd.peserta_id', (int) $pesertaId)
			->order_by('pd.created_at', 'DESC')
			->get()
			->result();
	}

	public function find($id)
	{
		return $this->dasar()
			->select($this->kolom(), FALSE)
			->where('pd.id', (int) $id)
			->get()
			->row();
	}

	/**
	 * Semua pendaftaran milik SATU akun users (lintas profil peserta),
	 * satu query — dipakai dashboard & halaman pendaftaran peserta.
	 */
	public function byUser($userId)
	{
		return $this->dasar()
			->select($this->kolom().', ps.nama AS peserta_nama', FALSE)
			->join('peserta ps', 'ps.id = pd.peserta_id')
			->where('ps.user_id', (int) $userId)
			->order_by('pd.created_at', 'DESC')
			->get()
			->result();
	}

	public function isExists($kelasId, $pesertaId)
	{
		return $this->db
			->where('kelas_id', (int) $kelasId)
			->where('peserta_id', (int) $pesertaId)
			->count_all_results('pendaftaran') > 0;
	}

	/** @return int ID pendaftaran baru, atau FALSE. */
	public function create($kelasId, $pesertaId, $status = 'menunggu')
	{
		$this->db->insert('pendaftaran', array(
			'kelas_id' => (int) $kelasId,
			'peserta_id' => (int) $pesertaId,
			'status' => $status,
		));
		return $this->db->insert_id() ?: FALSE;
	}

	public function setStatus($id, $status)
	{
		$this->db->where('id', (int) $id)->update('pendaftaran', array('status' => $status));
		return TRUE;
	}

	/** Peserta terdaftar (status) pada sebuah kelas — untuk detail kelas guru. */
	public function pesertaByKelas($kelasId, $status = 'aktif')
	{
		return $this->db
			->select('ps.*, pd.created_at AS sejak', FALSE)
			->from('pendaftaran pd')
			->join('peserta ps', 'ps.id = pd.peserta_id')
			->where('pd.kelas_id', (int) $kelasId)
			->where('pd.status', $status)
			->order_by('ps.nama', 'ASC')
			->get()
			->result();
	}

	/** @return int Jumlah pendaftaran aktif milik satu peserta. */
	public function countAktifByPeserta($pesertaId)
	{
		return (int) $this->db
			->where('peserta_id', (int) $pesertaId)
			->where('status', 'aktif')
			->count_all_results('pendaftaran');
	}

	/** Pendaftaran aktif milik peserta → [kelas_id, ...] (untuk cek akses link meeting). */
	public function kelasAktifByPeserta($pesertaId)
	{
		$rows = $this->db->select('kelas_id')
			->where('peserta_id', (int) $pesertaId)
			->where('status', 'aktif')
			->get('pendaftaran')->result();
		$out = array();
		foreach ($rows as $row) {
			$out[] = (int) $row->kelas_id;
		}
		return $out;
	}
}
