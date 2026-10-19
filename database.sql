-- =====================================================================
-- Al-Utsmani LMS — Skema Database + Seed
-- ---------------------------------------------------------------------
-- Cara import (pilih salah satu):
--   1. phpMyAdmin > Import > pilih file ini (nama database di atas file
--      dibuat otomatis oleh perintah CREATE DATABASE di bawah)
--   2. CLI: C:\xampp\mysql\bin\mysql.exe -u root < database.sql
--
-- Catatan:
--   * Semua tabel InnoDB + utf8mb4 (mendukung foreign key & emoji).
--   * Kolom snake_case, nilai enum Bahasa Indonesia.
--   * Nominal disimpan sebagai INTEGER (Rupiah bulat, tanpa desimal).
--   * Waktu disimpan apa adanya; aplikasi memakai zona Asia/Jakarta.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS al_utsmani_lms
	DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE al_utsmani_lms;

-- ---------------------------------------------------------------------
-- users — akun login (admin, guru, peserta/wali)
-- login menerima email ATAU username
-- ---------------------------------------------------------------------
CREATE TABLE users (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	nama VARCHAR(100) NOT NULL, -- nama pemilik akun (wali/admin); guru: sama dgn guru.nama
	username VARCHAR(50) NOT NULL,
	email VARCHAR(100) NOT NULL,
	password VARCHAR(255) NOT NULL, -- hash password_hash()
	role ENUM('admin','guru','peserta') NOT NULL DEFAULT 'peserta',
	status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_users_username (username),
	UNIQUE KEY uq_users_email (email),
	KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- program — di-seed, tanpa CRUD
-- ---------------------------------------------------------------------
CREATE TABLE program (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	kode VARCHAR(10) NOT NULL,
	nama VARCHAR(100) NOT NULL,
	deskripsi TEXT NULL,
	urutan TINYINT UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	UNIQUE KEY uq_program_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- level — di-seed, tanpa CRUD
-- ---------------------------------------------------------------------
CREATE TABLE level (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	nama VARCHAR(50) NOT NULL,
	urutan TINYINT UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	UNIQUE KEY uq_level_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- cabang — lokasi belajar, dikelompokkan per wilayah (12 lokasi)
-- ---------------------------------------------------------------------
CREATE TABLE cabang (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	wilayah VARCHAR(50) NOT NULL,
	nama VARCHAR(100) NOT NULL,
	status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_cabang_wilayah_nama (wilayah, nama),
	KEY idx_cabang_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- guru — profil guru, terikat satu akun users
-- ---------------------------------------------------------------------
CREATE TABLE guru (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id INT UNSIGNED NOT NULL,
	nama VARCHAR(100) NOT NULL,
	no_wa VARCHAR(20) NULL,
	status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_guru_user_id (user_id),
	KEY idx_guru_status (status),
	CONSTRAINT fk_guru_users FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- peserta — data peserta (bisa banyak per akun: wali dengan beberapa anak)
-- ---------------------------------------------------------------------
CREATE TABLE peserta (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id INT UNSIGNED NOT NULL, -- akun pemilik (peserta itu sendiri atau wali)
	nama VARCHAR(100) NOT NULL,
	tanggal_lahir DATE NULL,
	jenis_kelamin ENUM('L','P') NULL,
	no_wa VARCHAR(20) NULL,
	alamat VARCHAR(255) NULL,
	nama_wali VARCHAR(100) NULL, -- wajib diisi untuk peserta anak
	kemampuan_awal ENUM('pemula','hafalan') NOT NULL DEFAULT 'pemula',
	-- Level peserta BUKAN pilihan peserta: ditetapkan pengelola lewat proses
	-- penempatan (lihat tabel penempatan_level). NULL = belum ditempatkan.
	level_id INT UNSIGNED NULL,
	level_ditetapkan_at DATETIME NULL,
	level_ditetapkan_by INT UNSIGNED NULL, -- users.id admin yang menetapkan
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_peserta_user (user_id),
	KEY idx_peserta_nama (nama),
	KEY idx_peserta_level (level_id),
	CONSTRAINT fk_peserta_users FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
	CONSTRAINT fk_peserta_level FOREIGN KEY (level_id) REFERENCES level (id),
	CONSTRAINT fk_peserta_level_oleh FOREIGN KEY (level_ditetapkan_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- penempatan_level — riwayat penempatan/rank peserta oleh pengelola.
-- Level terkini peserta = baris terbaru; peserta.level_id menyimpan cache-nya.
-- ---------------------------------------------------------------------
CREATE TABLE penempatan_level (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	peserta_id INT UNSIGNED NOT NULL,
	level_id INT UNSIGNED NOT NULL,
	penilai_user_id INT UNSIGNED NOT NULL, -- admin yang menetapkan
	catatan VARCHAR(255) NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_penempatan_peserta (peserta_id, created_at),
	CONSTRAINT fk_penempatan_peserta FOREIGN KEY (peserta_id) REFERENCES peserta (id) ON DELETE CASCADE,
	CONSTRAINT fk_penempatan_level FOREIGN KEY (level_id) REFERENCES level (id),
	CONSTRAINT fk_penempatan_penilai FOREIGN KEY (penilai_user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- kelas — kombinasi program + level + cabang + mode + guru + jadwal + biaya
-- tipe_peserta & nama_instansi hanya dipakai untuk program BILAL
-- PDHQ: wajib cabang wilayah Bogor + mode offline (divalidasi aplikasi)
-- ---------------------------------------------------------------------
CREATE TABLE kelas (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	program_id INT UNSIGNED NOT NULL,
	level_id INT UNSIGNED NOT NULL,
	cabang_id INT UNSIGNED NOT NULL,
	guru_id INT UNSIGNED NOT NULL,
	mode ENUM('online','offline') NOT NULL,
	jadwal VARCHAR(255) NOT NULL, -- teks bebas, sertakan zona waktu (mis. "Sabtu 08.00-09.30")
	zona_waktu ENUM('WIB','WITA','WIT') NOT NULL DEFAULT 'WIB',
	link_meeting VARCHAR(255) NULL, -- hanya untuk kelas online; tampil utk peserta terdaftar + guru
	biaya_pendaftaran INT UNSIGNED NOT NULL DEFAULT 0,
	biaya_spp INT UNSIGNED NOT NULL DEFAULT 0,
	tipe_peserta ENUM('private','kelompok','perusahaan','instansi') NULL, -- khusus BILAL
	nama_instansi VARCHAR(100) NULL, -- khusus BILAL (opsional)
	status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_kelas_program (program_id),
	KEY idx_kelas_level (level_id),
	KEY idx_kelas_cabang (cabang_id),
	KEY idx_kelas_guru (guru_id),
	KEY idx_kelas_status (status),
	CONSTRAINT fk_kelas_program FOREIGN KEY (program_id) REFERENCES program (id),
	CONSTRAINT fk_kelas_level FOREIGN KEY (level_id) REFERENCES level (id),
	CONSTRAINT fk_kelas_cabang FOREIGN KEY (cabang_id) REFERENCES cabang (id),
	CONSTRAINT fk_kelas_guru FOREIGN KEY (guru_id) REFERENCES guru (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- pendaftaran — pendaftaran peserta ke sebuah kelas
-- status: menunggu (bukti belum diverifikasi) > aktif / ditolak / batal
-- UNIQUE mencegah pendaftaran ganda ke kelas yang sama
-- ---------------------------------------------------------------------
CREATE TABLE pendaftaran (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	kelas_id INT UNSIGNED NOT NULL,
	peserta_id INT UNSIGNED NOT NULL,
	status ENUM('menunggu','aktif','ditolak','batal') NOT NULL DEFAULT 'menunggu',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_pendaftaran_kelas_peserta (kelas_id, peserta_id),
	KEY idx_pendaftaran_peserta_status (peserta_id, status),
	KEY idx_pendaftaran_status (status),
	CONSTRAINT fk_pendaftaran_kelas FOREIGN KEY (kelas_id) REFERENCES kelas (id),
	CONSTRAINT fk_pendaftaran_peserta FOREIGN KEY (peserta_id) REFERENCES peserta (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- tagihan_spp — tagihan SPP bulanan (dibuat admin)
-- nominal diisi manual admin; UNIQUE menjamin 1 tagihan/peserta/bulan
-- jatuh_tempo default tanggal 10; tunggakan = belum lunas + lewat jatuh tempo
-- ---------------------------------------------------------------------
CREATE TABLE tagihan_spp (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	peserta_id INT UNSIGNED NOT NULL,
	periode VARCHAR(7) NOT NULL, -- 'YYYY-MM'
	nominal INT UNSIGNED NOT NULL,
	jatuh_tempo DATE NOT NULL,
	status ENUM('belum','lunas') NOT NULL DEFAULT 'belum',
	lunas_at DATETIME NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_tagihan_peserta_periode (peserta_id, periode),
	KEY idx_tagihan_status_tempo (status, jatuh_tempo),
	KEY idx_tagihan_periode (periode),
	CONSTRAINT fk_tagihan_peserta FOREIGN KEY (peserta_id) REFERENCES peserta (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- pembayaran — semua pembayaran manual (transfer) + bukti unggahan
-- jenis: pendaftaran (merujuk pendaftaran_id) atau spp (merujuk tagihan_spp_id)
-- alur: menunggu > diverifikasi (memicu status pendaftaran/tagihan) atau ditolak
-- pemasukan = SUM(jumlah) where status='diverifikasi' per periode verified_at
-- ---------------------------------------------------------------------
CREATE TABLE pembayaran (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	peserta_id INT UNSIGNED NOT NULL, -- peserta yang menjadi objek pembayaran
	user_id INT UNSIGNED NOT NULL, -- akun yang mengunggah bukti
	jenis ENUM('pendaftaran','spp') NOT NULL,
	pendaftaran_id INT UNSIGNED NULL,
	tagihan_spp_id INT UNSIGNED NULL,
	jumlah INT UNSIGNED NOT NULL,
	bukti_file VARCHAR(255) NOT NULL, -- nama file acak di uploads/bukti_pembayaran/
	status ENUM('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
	catatan_admin VARCHAR(255) NULL,
	verified_by INT UNSIGNED NULL, -- users.id admin
	verified_at DATETIME NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_pembayaran_status_jenis (status, jenis),
	KEY idx_pembayaran_peserta (peserta_id),
	KEY idx_pembayaran_pendaftaran (pendaftaran_id),
	KEY idx_pembayaran_tagihan (tagihan_spp_id),
	KEY idx_pembayaran_verified_at (verified_at),
	CONSTRAINT fk_pembayaran_peserta FOREIGN KEY (peserta_id) REFERENCES peserta (id),
	CONSTRAINT fk_pembayaran_user FOREIGN KEY (user_id) REFERENCES users (id),
	CONSTRAINT fk_pembayaran_pendaftaran FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran (id),
	CONSTRAINT fk_pembayaran_tagihan FOREIGN KEY (tagihan_spp_id) REFERENCES tagihan_spp (id),
	CONSTRAINT fk_pembayaran_verifier FOREIGN KEY (verified_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- pertemuan — satu kelas bisa banyak pertemuan (dibuat guru)
-- ---------------------------------------------------------------------
CREATE TABLE pertemuan (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	kelas_id INT UNSIGNED NOT NULL,
	nomor SMALLINT UNSIGNED NOT NULL,
	tanggal DATE NOT NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_pertemuan_kelas_nomor (kelas_id, nomor),
	CONSTRAINT fk_pertemuan_kelas FOREIGN KEY (kelas_id) REFERENCES kelas (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- absensi — satu baris per peserta per pertemuan:
-- status kehadiran + progres belajar (halaman mulai–selesai + catatan guru)
-- diisi dalam satu form oleh guru per pertemuan
-- ---------------------------------------------------------------------
CREATE TABLE absensi (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	pertemuan_id INT UNSIGNED NOT NULL,
	peserta_id INT UNSIGNED NOT NULL,
	status ENUM('hadir','izin','sakit','alpa') NOT NULL,
	hal_mulai INT UNSIGNED NULL, -- halaman awal pada pertemuan ini
	hal_selesai INT UNSIGNED NULL, -- halaman akhir pada pertemuan ini
	catatan_guru VARCHAR(255) NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY uq_absensi_pertemuan_peserta (pertemuan_id, peserta_id),
	KEY idx_absensi_peserta (peserta_id),
	CONSTRAINT fk_absensi_pertemuan FOREIGN KEY (pertemuan_id) REFERENCES pertemuan (id) ON DELETE CASCADE,
	CONSTRAINT fk_absensi_peserta FOREIGN KEY (peserta_id) REFERENCES peserta (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- SEED
-- =====================================================================

INSERT INTO program (kode, nama, deskripsi, urutan) VALUES
('BIHAR', 'Bimbingan Tahfidz Anak dan Remaja', 'Program tahfidz untuk anak dan remaja, tersedia offline maupun online.', 1),
('BADAL', 'Bimbingan dalam Lembaga', 'Belajar Al-Qur''an untuk dewasa di lingkungan lembaga, offline maupun online.', 2),
('BILAL', 'Bimbingan luar Lembaga', 'Layanan di luar lembaga: private, kelompok, perusahaan, atau instansi.', 3),
('PDHQ', 'Pendidikan Dai Hafidz Qur''an', 'Program khusus wilayah Bogor untuk santri muqim (offline).', 4);

INSERT INTO level (nama, urutan) VALUES
('Idad', 1),
('Level 1', 2),
('Level 2', 3),
('Level 3', 4),
('Level 4', 5),
('Level 5', 6),
('Talaqi', 7),
('Tahfidz', 8);

INSERT INTO cabang (wilayah, nama, status) VALUES
('Jakarta Timur', 'Condet', 'aktif'),
('Jakarta Timur', 'Dukuh', 'aktif'),
('Jakarta Timur', 'Pinang Ranti', 'aktif'),
('Bekasi', 'Jati Asih', 'aktif'),
('Bekasi', 'Jati Sampurna', 'aktif'),
('Cirebon', 'Cirebon', 'aktif'),
('Padang', 'Nanggalo', 'aktif'),
('Padang', 'Air Tawar', 'aktif'),
('Padang', 'Padang Utara', 'aktif'),
('Bogor', 'Tamansari', 'aktif'),
('Bogor', 'Megamendung', 'aktif'),
('Sorong', 'Sorong', 'aktif');

-- Akun admin awal.
-- Username : admin
-- Email    : admin@al-utsmani.id
-- Password : admin123
-- >>> WAJIB GANTI PASSWORD SETELAH DEPLOY / PERTAMA KALI LOGIN. <<<
INSERT INTO users (nama, username, email, password, role, status) VALUES
('Administrator', 'admin', 'admin@al-utsmani.id', '$2y$10$jldF1FZQjxCpf47TFb.gAeDJZGWqs1ZFstjJJ/yOGt1bHUSNlBXmO', 'admin', 'aktif');
