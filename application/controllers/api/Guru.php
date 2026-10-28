<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends MY_Api_Controller {

	private $guru = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Guru_model');
		$this->load->model('Kelas_model');
		$this->load->model('Pertemuan_model');
		$this->load->model('Absensi_model');
		$this->load->model('Pendaftaran_model');
		$this->load->model('Peserta_model');
		
		$this->guru = $this->requireGuru();
	}

	public function dashboard()
	{
		$guru_id = $this->guru->id;
		
		// Kelas aktif yang diampu
		$kelas = $this->db
			->select('k.*, p.kode AS program_kode, p.nama AS program_nama, l.nama AS level_nama, c.nama AS cabang_nama')
			->from('kelas k')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('k.guru_id', $guru_id)
			->where('k.status', 'aktif')
			->get()
			->result();

		// Jumlah pertemuan
		$jumlah_pertemuan = $this->Pertemuan_model->jumlahByGuru($guru_id);

		// Jumlah peserta aktif
		$this->db->select('COUNT(DISTINCT pd.peserta_id) AS total')
			->from('pendaftaran pd')
			->join('kelas k', 'k.id = pd.kelas_id')
			->where('k.guru_id', $guru_id)
			->where('pd.status', 'aktif');
		$peserta = $this->db->get()->row();
		$jumlah_peserta = $peserta ? (int) $peserta->total : 0;

		$this->response(array(
			'guru' => array(
				'id' => (int) $this->guru->id,
				'nama' => $this->guru->nama,
			),
			'jumlah_kelas' => count($kelas),
			'jumlah_pertemuan' => $jumlah_pertemuan,
			'jumlah_peserta' => $jumlah_peserta,
			'kelas' => $kelas,
		), 200, 'Dashboard guru berhasil diambil');
	}

	public function jadwal()
	{
		$kelas = $this->db
			->select('k.*, p.kode AS program_kode, p.nama AS program_nama, l.nama AS level_nama, c.nama AS cabang_nama, c.wilayah')
			->from('kelas k')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('k.guru_id', $this->guru->id)
			->where('k.status', 'aktif')
			->order_by('k.id', 'ASC')
			->get()
			->result();

		$this->response($kelas, 200, 'Jadwal berhasil diambil');
	}

	public function kelas()
	{
		$kelas = $this->db
			->select('k.*, p.kode AS program_kode, p.nama AS program_nama, l.nama AS level_nama, c.nama AS cabang_nama')
			->from('kelas k')
			->join('program p', 'p.id = k.program_id')
			->join('level l', 'l.id = k.level_id')
			->join('cabang c', 'c.id = k.cabang_id')
			->where('k.guru_id', $this->guru->id)
			->get()
			->result();

		$result = array();
		foreach ($kelas as $k) {
			// Hitung peserta
			$jumlah_peserta = $this->db
				->where('kelas_id', $k->id)
				->where('status', 'aktif')
				->count_all_results('pendaftaran');
			
			// Hitung pertemuan
			$jumlah_pertemuan = $this->db
				->where('kelas_id', $k->id)
				->count_all_results('pertemuan');

			$result[] = array(
				'id' => (int) $k->id,
				'program' => $k->program_nama . ' (' . $k->program_kode . ')',
				'level' => $k->level_nama,
				'cabang' => $k->cabang_nama,
				'mode' => $k->mode,
				'jadwal' => $k->jadwal,
				'zona_waktu' => $k->zona_waktu,
				'status' => $k->status,
				'jumlah_peserta' => $jumlah_peserta,
				'jumlah_pertemuan' => $jumlah_pertemuan,
			);
		}

		$this->response($result, 200, 'Kelas berhasil diambil');
	}

	public function peserta($kelas_id)
	{
		$kelas_id = (int) $kelas_id;
		
		// Validasi kelas milik guru ini
		$kelas = $this->db
			->where('id', $kelas_id)
			->where('guru_id', $this->guru->id)
			->get('kelas')
			->row();
		
		if (! $kelas) {
			$this->error('Kelas tidak ditemukan atau bukan milik Anda', 403);
		}

		$peserta = $this->db
			->select('ps.*, pd.status AS status_pendaftaran, l.nama AS level_nama, l.urutan AS level_urutan')
			->from('pendaftaran pd')
			->join('peserta ps', 'ps.id = pd.peserta_id')
			->join('level l', 'l.id = ps.level_id', 'left')
			->where('pd.kelas_id', $kelas_id)
			->where('pd.status', 'aktif')
			->order_by('ps.nama', 'ASC')
			->get()
			->result();

		$result = array();
		foreach ($peserta as $p) {
			$result[] = array(
				'id' => (int) $p->id,
				'nama' => $p->nama,
				'tanggal_lahir' => $p->tanggal_lahir,
				'jenis_kelamin' => $p->jenis_kelamin,
				'no_wa' => $p->no_wa,
				'level_id' => $p->level_id ? (int) $p->level_id : null,
				'level_nama' => $p->level_nama,
				'level_urutan' => $p->level_urutan ? (int) $p->level_urutan : null,
				'kemampuan_awal' => $p->kemampuan_awal,
			);
		}

		$this->response($result, 200, 'Peserta kelas berhasil diambil');
	}

	public function pertemuan($kelas_id)
	{
		$kelas_id = (int) $kelas_id;
		
		$kelas = $this->db
			->where('id', $kelas_id)
			->where('guru_id', $this->guru->id)
			->get('kelas')
			->row();
		
		if (! $kelas) {
			$this->error('Kelas tidak ditemukan atau bukan milik Anda', 403);
		}

		if ($this->input->method() === 'get') {
			$pertemuan = $this->Pertemuan_model->byKelasWithCounts($kelas_id);
			$this->response($pertemuan, 200, 'Pertemuan berhasil diambil');
		} elseif ($this->input->method() === 'post') {
			$nomor = $this->Pertemuan_model->nextNomor($kelas_id);
			$tanggal = $this->input->post('tanggal');
			
			if (! $tanggal) {
				$this->error('Tanggal wajib diisi', 400);
			}

			$id = $this->Pertemuan_model->create($kelas_id, $nomor, $tanggal);
			if (! $id) {
				$this->error('Gagal membuat pertemuan. Nomor mungkin sudah ada.', 400);
			}

			$this->response(array('id' => $id, 'nomor' => $nomor), 201, 'Pertemuan berhasil dibuat');
		}
	}

	public function absensi($pertemuan_id)
	{
		$pertemuan_id = (int) $pertemuan_id;
		$pertemuan = $this->Pertemuan_model->find($pertemuan_id);
		
		if (! $pertemuan) {
			$this->error('Pertemuan tidak ditemukan', 404);
		}
		
		if ((int) $pertemuan->guru_id !== (int) $this->guru->id) {
			$this->error('Tidak memiliki akses ke pertemuan ini', 403);
		}

		if ($this->input->method() === 'get') {
			// Ambil peserta kelas
			$peserta = $this->db
				->select('ps.id, ps.nama')
				->from('pendaftaran pd')
				->join('peserta ps', 'ps.id = pd.peserta_id')
				->where('pd.kelas_id', $pertemuan->kelas_id)
				->where('pd.status', 'aktif')
				->order_by('ps.nama', 'ASC')
				->get()
				->result();

			// Ambil data absensi yang sudah ada
			$absensi = $this->Pertemuan_model->isian($pertemuan_id);
			$absensi_map = array();
			foreach ($absensi as $a) {
				$absensi_map[$a->peserta_id] = $a;
			}

			$result = array();
			foreach ($peserta as $p) {
				$ada = isset($absensi_map[$p->id]) ? $absensi_map[$p->id] : null;
				$result[] = array(
					'peserta_id' => (int) $p->id,
					'peserta_nama' => $p->nama,
					'status' => $ada ? $ada->status : null,
					'hal_mulai' => $ada && $ada->hal_mulai ? (int) $ada->hal_mulai : null,
					'hal_selesai' => $ada && $ada->hal_selesai ? (int) $ada->hal_selesai : null,
					'catatan_guru' => $ada ? $ada->catatan_guru : null,
				);
			}

			$this->response(array(
				'pertemuan' => $pertemuan,
				'peserta' => $result,
			), 200, 'Data absensi berhasil diambil');
		} elseif ($this->input->method() === 'post') {
			$rows = $this->input->post('absensi');
			if (! is_array($rows) || empty($rows)) {
				$this->error('Data absensi wajib diisi', 400);
			}

			$absensi_data = array();
			foreach ($rows as $r) {
				if (! isset($r['peserta_id']) || ! isset($r['status'])) {
					continue;
				}
				$absensi_data[] = array(
					'peserta_id' => (int) $r['peserta_id'],
					'status' => $r['status'],
					'hal_mulai' => isset($r['hal_mulai']) && $r['hal_mulai'] !== '' ? (int) $r['hal_mulai'] : NULL,
					'hal_selesai' => isset($r['hal_selesai']) && $r['hal_selesai'] !== '' ? (int) $r['hal_selesai'] : NULL,
					'catatan_guru' => isset($r['catatan_guru']) ? trim($r['catatan_guru']) : '',
				);
			}

			if (empty($absensi_data)) {
				$this->error('Tidak ada data valid untuk disimpan', 400);
			}

			$saved = $this->Absensi_model->simpanBulk($pertemuan_id, $absensi_data);
			if (! $saved) {
				$this->error('Gagal menyimpan absensi', 500);
			}

			$this->response(null, 200, 'Absensi dan laporan perkembangan berhasil disimpan');
		}
	}

	public function laporan_perkembangan($pertemuan_id)
	{
		// Alias untuk absensi, karena sudah digabung
		return $this->absensi($pertemuan_id);
	}
}
