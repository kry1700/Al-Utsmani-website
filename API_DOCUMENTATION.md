# Dokumentasi API LBQ Al-Utsmani Mobile

Dokumentasi ini untuk aplikasi mobile (guru & peserta/wali) yang mengonsumsi API CodeIgniter 3 yang telah dibuat.

## Informasi Umum

- **Base URL**: `http://localhost/Al-Utsmani/api`
- **Format Response**: JSON
- **Autentikasi**: Bearer Token (untuk endpoint yang membutuhkan login)
- **CORS**: Sudah diaktifkan (Allow-Origin: *)
- **Content-Type**: `application/json` untuk request JSON, `multipart/form-data` untuk upload file

## Format Response

Semua response menggunakan format konsisten:

```json
{
  "success": true|false,
  "message": "Pesan response",
  "data": {...} | null,
  "errors": {...} // hanya ada jika success false dan ada validasi error
}
```

## Autentikasi

Untuk endpoint yang membutuhkan autentikasi, kirim header berikut:

```http
Authorization: Bearer <token>
```

Token didapat dari endpoint `POST /api/auth/login`. Token berlaku 30 hari.

---

## 1. Publik (Tanpa Login)

### 1.1 Beranda
Menampilkan data untuk landing page mobile.

**Endpoint:** `GET /beranda`  
**Auth:** Tidak diperlukan

**Response:**

```json
{
  "success": true,
  "message": "Beranda berhasil diambil",
  "data": {
    "programs": [
      {
        "id": 1,
        "kode": "BIHAR",
        "nama": "BIHAR",
        "deskripsi": "...",
        "urutan": 1,
        "jumlah_kelas": 5
      }
    ],
    "cabangs": [
      {
        "id": 1,
        "wilayah": "Jakarta Timur",
        "nama": "Condet"
      }
    ]
  }
}
```

### 1.2 List Program
Menampilkan daftar semua program beserta rekap kelas.

**Endpoint:** `GET /katalog/program`  
**Auth:** Tidak diperlukan

**Response:**

```json
{
  "success": true,
  "message": "Program berhasil diambil",
  "data": [
    {
      "id": 1,
      "kode": "BIHAR",
      "nama": "BIHAR",
      "deskripsi": "...",
      "urutan": 1,
      "jumlah_kelas": 5,
      "cabang": ["Condet", "Dukuh"]
    }
  ]
}
```

### 1.3 Detail Program & Kelas
Menampilkan daftar kelas per program.

**Endpoint:** `GET /katalog/program/{id}`  
**Auth:** Tidak diperlukan

**Response:**

```json
{
  "success": true,
  "message": "Detail program berhasil diambil",
  "data": {
    "program": {
      "id": 1,
      "kode": "BIHAR",
      "nama": "BIHAR",
      "deskripsi": "..."
    },
    "kelas": [
      {
        "id": 10,
        "program_kode": "BIHAR",
        "program_nama": "BIHAR",
        "level_nama": "Level 1",
        "level_urutan": 1,
        "cabang_nama": "Condet",
        "cabang_wilayah": "Jakarta Timur",
        "mode": "offline",
        "jadwal": "Sabtu 08.00-09.30 WIB",
        "zona_waktu": "WIB",
        "biaya_pendaftaran": 0,
        "biaya_spp": 150000,
        "guru_nama": "Ustadz Ahmad"
      }
    ]
  }
}
```

### 1.4 Detail Kelas
Menampilkan detail lengkap kelas.

**Endpoint:** `GET /katalog/kelas/{id}`  
**Auth:** Tidak diperlukan

**Response:**

```json
{
  "success": true,
  "message": "Detail kelas berhasil diambil",
  "data": {
    "id": 10,
    "program": {
      "kode": "BIHAR",
      "nama": "BIHAR"
    },
    "level": {
      "nama": "Level 1"
    },
    "cabang": {
      "nama": "Condet",
      "wilayah": "Jakarta Timur"
    },
    "guru": {
      "nama": "Ustadz Ahmad"
    },
    "mode": "offline",
    "jadwal": "Sabtu 08.00-09.30 WIB",
    "zona_waktu": "WIB",
    "link_meeting": null,
    "biaya_pendaftaran": 0,
    "biaya_spp": 150000,
    "tipe_peserta": null,
    "nama_instansi": null,
    "status": "aktif",
    "jumlah_peserta_aktif": 12
  }
}
```

> **Catatan:** Saat user memilih kelas untuk mendaftar, aplikasi mobile wajib mengarahkan ke halaman login/register jika belum login.

---

## 2. Autentikasi

### 2.1 Login
Login menggunakan email atau username.

**Endpoint:** `POST /auth/login`  
**Auth:** Tidak diperlukan  
**Content-Type:** `application/x-www-form-urlencoded` atau `application/json`

**Body:**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| identifier | string | Ya | Email atau Username |
| password | string | Ya | Password |

**Contoh Request (JSON):**

```json
{
  "identifier": "user@example.com",
  "password": "password123"
}
```

**Response Sukses:**

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "a1b2c3d4e5f6...",
    "expires_at": "2026-11-27 10:30:00",
    "user": {
      "id": 5,
      "nama": "Ahmad Fauzi",
      "username": "ahmadfauzi",
      "email": "ahmad@example.com",
      "role": "peserta"
    }
  }
}
```

**Response Error:**

```json
{
  "success": false,
  "message": "Email/username atau password salah.",
  "data": null
}
```

### 2.2 Registrasi Akun Peserta/Wali
Membuat akun baru dengan role peserta/wali. Satu akun bisa menaungi banyak profil peserta (anak).

**Endpoint:** `POST /auth/register`  
**Auth:** Tidak diperlukan  
**Content-Type:** `application/x-www-form-urlencoded` atau `application/json`

**Body:**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| nama | string | Ya | Nama lengkap pemilik akun/wali |
| username | string | Ya | Username (huruf, angka, underscore, titik) |
| email | string | Ya | Email valid |
| no_wa | string | Ya | Nomor WhatsApp (6-20 karakter, angka/+/ -/spasi) |
| password | string | Ya | Minimal 8 karakter |
| konfirmasi_password | string | Ya | Harus sama dengan password |

**Contoh Request (JSON):**

```json
{
  "nama": "Ahmad Fauzi",
  "username": "ahmadfauzi",
  "email": "ahmad@example.com",
  "no_wa": "081234567890",
  "password": "password123",
  "konfirmasi_password": "password123"
}
```

**Response Sukses:**

```json
{
  "success": true,
  "message": "Registrasi berhasil. Silakan login.",
  "data": {
    "peserta_id": 10
  }
}
```

### 2.3 Logout
Logout dan menghapus token.

**Endpoint:** `POST /auth/logout`  
**Auth:** Wajib (Bearer Token)

**Response:**

```json
{
  "success": true,
  "message": "Logout berhasil",
  "data": null
}
```

### 2.4 Info User Saat Ini (Penting!)
**Endpoint ini wajib dipanggil setelah login** untuk menentukan tampilan menu di aplikasi mobile.

Menampilkan data user, status `is_guru`, data guru (jika ada), dan daftar profil peserta.

**Endpoint:** `GET /me`  
**Auth:** Wajib (Bearer Token)

**Response:**

```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": {
    "user": {
      "id": 5,
      "nama": "Ahmad Fauzi",
      "username": "ahmadfauzi",
      "email": "ahmad@example.com",
      "role": "peserta",
      "status": "aktif"
    },
    "is_guru": true,
    "guru": {
      "id": 2,
      "nama": "Ustadz Ahmad Fauzi",
      "no_wa": "081234567890",
      "status": "aktif"
    },
    "profil_peserta": [
      {
        "id": 10,
        "nama": "Ali",
        "tanggal_lahir": "2015-05-10",
        "jenis_kelamin": "L",
        "level_id": 1,
        "level_nama": "Level 1"
      },
      {
        "id": 11,
        "nama": "Fatimah",
        "tanggal_lahir": "2017-08-15",
        "jenis_kelamin": "P",
        "level_id": null,
        "level_nama": null
      }
    ]
  }
}
```

> **PENTING:** 
> - **Menu "Guru" HANYA muncul jika `is_guru === true`**. Cek berdasarkan keberadaan record aktif di tabel `guru`, bukan hanya dari `user.role`.
> - Jika user hanya peserta (bukan guru), maka `is_guru` akan `false` dan menu guru **TIDAK BOLEH** ditampilkan sama sekali.
> - Jika user terda', na( `is_guru` `true` dan data `guru` ada, maka menu guru **BOLEH** ditampilkan.

---

## 3. Peserta/Wali

Semua endpoint di bawah ini wajib login.

### 3.1 Dashboard Peserta
Menampilkan ringkasan pendaftaran aktif, tagihan belum lunas, dan daftar profil peserta.

**Endpoint:** `GET /peserta/dashboard`  
**Auth:** Wajib

**Response:**

```json
{
  "success": true,
  "message": "Dashboard peserta berhasil diambil",
  "data": {
    "profil_peserta": [
      {
        "id": 10,
        "nama": "Ali",
        "tanggal_lahir": "2015-05-10",
        "jenis_kelamin": "L",
        "no_wa": "081234567890",
        "nama_wali": "Ahmad Fauzi",
        "kemampuan_awal": "pemula",
        "level_id": 1,
        "user_id": 5
      }
    ],
    "pendaftaran": [
      {
        "id": 25,
        "kelas_id": 10,
        "peserta_id": 10,
        "status": "aktif",
        "created_at": "2026-10-28 10:00:00",
        "jadwal": "Sabtu 08.00-09.30 WIB",
        "mode": "offline",
        "link_meeting": null,
        "program_nama": "BIHAR",
        "program_kode": "BIHAR",
        "level_nama": "Level 1",
        "cabang_nama": "Condet"
      }
    ],
    "tagihan": [
      {
        "id": 15,
        "peserta_id": 10,
        "periode": "2026-11",
        "nominal": 150000,
        "jatuh_tempo": "2026-11-10",
        "status": "belum",
        "created_at": "2026-10-28 10:00:00"
      }
    ],
    "jumlah_tagihan_belum_lunas": 1
  }
}
```

### 3.2 Daftar Kelas
Mendaftarkan salah satu profil peserta ke kelas yang dipilih.

**Endpoint:** `POST /peserta/daftar-kelas`  
**Auth:** Wajib  
**Content-Type:** `application/x-www-form-urlencoded` atau `application/json`

**Body:**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| kelas_id | integer | Ya | ID kelas yang ingin didaftarkan |
| peserta_id | integer | Ya | ID profil peserta (harus milik akun yang login) |

**Contoh Request (JSON):**

```json
{
  "kelas_id": 10,
  "peserta_id": 10
}
```

**Response Sukses (Gratis):**

```json
{
  "success": true,
  "message": "Pendaftaran berhasil (gratis)",
  "data": {
    "pendaftaran_id": 25,
    "status": "aktif",
    "perlu_bayar_pendaftaran": false,
    "biaya_pendaftaran": 0
  }
}
```

**Response Sukses (Berbayar):**

```json
{
  "success": true,
  "message": "Pendaftaran berhasil, menunggu pembayaran",
  "data": {
    "pendaftar!n id": 26,
    "status": "menunggu",
    "perlu_bayar_pendaftaran": true,
    "biaya_pendaftaran": 100000
  }
}
```

### 3.3 List Tagihan
Menampilkan semua tagihan SPP dari semua profil peserta milik akun.

**Endpoint:** `GET /peserta/tagihan`  
**Auth:** Wajib

**Response:**

```json
{
  "success": true,
  "message": "Tagihan berhasil diambil",
  "data": [
    {
      "id": 15,
      "peserta_id": 10,
      "periode": "2026-11",
      "nominal": 150000,
      "jatuh_tempo": "2026-11-10",
      "status": "belum",
      "lunas_at": null,
      "created_at": "2026-10-28 10:00:00",
      "peserta_nama": "Ali"
    },
    {
      "id": 16,
      "peserta_id": 10,
      "periode": "2026-10",
      "nominal": 150000,
      "jatuh_tempo": "2026-10-10",
      "status": "lunas",
      "lunas_at": "2026-10-15 14:30:00",
      "created_at": "2026-10-01 10:00:00",
      "peserta_nama": "Ali"
    }
  ]
}
```

---

## 4. Pembayaran (Transaksi Mobile)

### 4.1 Upload Bukti Pembayaran
Upload bukti transfer untuk pembayaran pendaftaran atau SPP.

**Endpoint:** `POST /pembayaran/upload-bukti`  
**Auth:** Wajib  
**Content-Type:** `multipart/form-data`

**Body (form-data):**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| jenis | string | Ya | `pendaftaran` atau `spp` |
| peserta_id | integer | Ya | ID profil peserta milik akun |
| jumlah | integer | Ya | Jumlah yang dibayarkan |
| bukti | file | Ya | File bukti (JPG, JPEG, PNG, PDF). Maksimal 2MB |
| pendaftaran_id | integer | Jika jenis=pendaftaran | ID pendaftaran |
| tagihan_spp_id | integer | Jika jenis=spp | ID tagihan SPP |

**Contoh:**

```http
POST /api/pembayaran/upload-bukti
Authorization: Bearer <token>
Content-Type: multipart/form-data

jenis: spp
peserta_id: 10
jumlah: 150000
tagihan_spp_id: 15
bukti: [file.jpg]
```

**Response Sukses:**

```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diunggah, menunggu verifikasi admin",
  "data": {
    "pembayaran_id": 30,
    "bukti_file": "a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6.jpg",
    "status": "menunggu"
  }
}
```

**Response Error:**

```json
{
  "success": false,
  "message": "Ukuran file melebihi batas maksimal 2 MB.",
  "data": null
}
```

### 4.2 Lihat Bukti Pembayaran
Menampilkan/mengakses bukti pembayaran. Hanya bisa diakses oleh pemilik pembayaran atau admin.

**Endpoint:** `GET /pembayaran/bukti/{id}`  
**Auth:** Wajib

**Response:** File gambar (image/jpeg, image/png) atau PDF dengan header `Content-Disposition: inline` (untuk gambar) atau `attachment` (untuk PDF).

> **Catatan:** Endpoint ini mengembalikan file langsung, bukan JSON. Untuk menampilkan di aplikasi mobile bisa langsung load URL dengan header Authorization.

---

## 5. Guru (Hanya Jika Terdaftar Sebagai Guru)

**Semua endpoint di bawah ini hanya bisa diakses oleh user yang terdaftar aktif di tabel `guru`.**

Jika user hanya peserta, maka akan mendapat response error `403 Forbidden`.

### 5.1 Dashboard Guru
Ringkasan kelas yang diampu, jumlah pertemuan, jumlah peserta.

**Endpoint:** `GET /guru/dashboard`  
**Auth:** Wajib + Harus terdaftar sebagai guru aktif

**Response:**

```json
{
  "success": true,
  "message": "Dashboard guru berhasil diambil",
  "data": {
    "guru": {
      "id": 2,
      "nama": "Ustadz Ahmad Fauzi"
    },
    "jumlah_kelas": 2,
    "jumlah_pertemuan": 15,
    "jumlah_peserta": 24,
    "kelas": [
      {
        "id": 10,
        "program_id": 1,
        "level_id": 1,
        "cabang_id": 1,
        "guru_id": 2,
        "mode": "offline",
        "jadwal": "Sabtu 08.00-09.30 WIB",
        "zona_waktu": "WIB",
        "status": "aktif",
        "program_kode": "BIHAR",
        "program_nama": "BIHAR",
        "level_nama": "Level 1",
        "cabang_nama": "Condet"
      }
    ]
  }
}
```

### 5.2 Jadwal Kelas Guru
Menampilkan jadwal semua kelas yang diampu.

**Endpoint:** `GET /guru/jadwal`  
**Auth:** Wajib + Guru aktif

**Response:**

```json
{
  "success": true,
  "message": "Jadwal berhasil diambil",
  "data": [
    {
      "id": 10,
      "program_id": 1,
      "level_id": 1,
      "cabang_id": 1,
      "guru_id": 2,
      "mode": "offline",
      "jadwal": "Sabtu 08.00-09.30 WIB",
      "zona_waktu": "WIB",
      "link_meeting": null,
      "status": "aktif",
      "program_kode": "BIHAR",
      "program_nama": "BIHAR",
      "level_nama": "Level 1",
      "cabang_nama": "Condet",
      "wilayah": "Jakarta Timur"
    }
  ]
}
```

### 5.3 Daftar Kelas yang Diampu
Menampilkan semua kelas (aktif dan nonaktif) yang diampu guru beserta statistik.

**Endpoint:** `GET /guru/kelas`  
**Auth:** Wajib + Guru aktif

**Response:**

```json
{
  "success": true,
  "message": "Kelas berhasil diambil",
  "data": [
    {
      "id": 10,
      "program": "BIHAR (BIHAR)",
      "level": "Level 1",
      "cabang": "Condet",
      "mode": "offline",
      "jadwal": "Sabtu 08.00-09.30 WIB",
      "zona_wawa": "WIB",
      "status": "aktif",
      "jumlah_peserta": 12,
      "jumlah_pertemuan": 8
    }
  ]
}
```

### 5.4 Peserta Per Kelas
Menampilkan daftar peserta aktif per kelas beserta level mereka.

**Endpoint:** `GET /guru/kelas/{id}/peserta`  
**Auth:** Wajib + Guru aktif  
**Note:** Kelas harus milik guru yang bersangkutan.

**Response:**

```json
{
  "success": true,
  "message": "Peserta kelas berhasil diambil",
  "data": [
    {
      "id": 10,
      "nama": "Ali",
      "tanggal_lahir": "2015-05-10",
      "jenis_kelamin": "L",
      "no_wa": "081234567890",
      "level_id": 1,
      "level_nama": "Level 1",
      "level_urutan": 1,
      "kemampuan_awal": "pemula"
    },
    {
      "id": 11,
      "nama": "Fatimah",
      "tanggal_lahir": "2017-08-15",
      "jenis_kelamin": "P",
      "level_id": null,
      "level_nama": null,
      "level_urutan": null,
      "kemampuan_awal": "pemula"
    }
  ]
}
```

> **Catatan:** Ada peserta yang belum ditempatkan levelnya (`level_id` dan `level_nama` bisa `null`). Hal ini sesuai aturan bisnis proyek.

### 5.5 Pertemuan Kelas
Membuat dan melihat daftar pertemuan per kelas.

**Endpoint:** `GET /guru/pertemuan/{kelas_id}` | `POST /guru/pertemuan/{kelas_id}`  
**Auth:** Wajib + Guru aktif  
**Note:** Kelas harus milik guru yang bersangkutan.

#### GET - List Pertemuan
**Response:**

```json
{
  "success": true,
  "message": "Pertemuan berhasil diambil",
  "data": [
    {
      "id": 45,
      "kelas_id": 10,
      "nomor": 1,
      "tanggal": "2026-10-04",
      "created_at": "2026-10-04 08:30:00",
      "terisi": 12
    },
    {
      "id": 46,
      "kelas_id": 10,
      "nomor": 2,
      "tanggal": "2026-10-11",
      "created_at": "2026-10-11 08:30:00",
      "terisi": 0
    }
  ]
}
```

> `terisi` = jumlah peserta yang sudah diisi absensinya (ada baris di tabel `absensi`).

#### POST - Buat Pertemuan Baru
**Content-Type:** `application/x-www-form-urlencoded` atau `application/json`

**Body:**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| tanggal | date | Ya | Tanggal pertemuan (YYYY-MM-DD) |

**Contoh Request (JSON):**

```json
{
  "tanggal": "2026-11-01"
}
```

**Response Sukses:**

```json
{
  "success": true,
  "message": "Pertemuan berhasil dibuat",
  "data": {
    "id": 47,
    "nomor": 3
  }
}
```

### 5.6 Absensi & Laporan Perkembangan Per Sesi
**Fitur utama untuk guru:** Mengisi absensi sekaligus laporan perkembangan tiap anak per sesi.

Endpoint ini menggabungkan absensi dan progres belajar (sesuai tabel `absensi` yang digabung).

**Endpoint:** `GET /guru/absensi/{pertemuan_id}` | `POST /guru/absensi/{pertemuan_id}`  
**Auth:** Wajib + Guru aktif  
**Note:** Pertemuan harus milik kelas yang diampu guru yang bersangkutan.

#### GET - Form Absensi & Laporan Perkembangan
Menampilkan daftar peserta beserta data absensi yang sudah ada (untuk prefill jika sudah pernah diisi).

**Response:**

```json
{
  "success": true,
  "message": "Data absensi berhasil diambil",
  "data": {
    "pertemuan": {
      "id": 45,
      "kelas_id": 10,
      "nomor": 1,
      "tanggal": "2026-10-04",
      "guru_id": 2,
      "kelas_status": "aktif",
      "mode": "offline",
      "jadwal": "Sabtu 08.00-09.30 WIB",
      "zona_waktu": "WIB",
      "program_kode": "BIHAR",
      "program_nama": "BIHAR",
      "level_nama": "Level 1",
      "cabang_nama": "Condet",
      "cabang_wilayah": "Jakarta Timur"
    },
    "peserta": [
      {
        "peserta_id": 10,
        "peserta_nama": "Ali",
        "status": "hadir",
        "hal_mulai": 5,
        "hal_selesai": 10,
        "catatan_guru": "Sangat bagus, lancar membaca"
      },
      {
        "peserta_id": 11,
        "peserta_nama": "Fatimah",
        "status": "hadir",
        "hal_mulai": 2,
        "hal_selesai": 5,
        "catatan_guru": "Perlu latihan tajwid lebih lanjut"
      },
      {
        "peserta_id": 12,
        "peserta_nama": "Zain",
        "status": "izin",
        "hal_mulai": null,
        "hal_selesai": null,
        "catatan_guru": "Izin karena sakit"
      }
    ]
  }
}
```

#### POST - Simpan Absensi & Laporan Perkembangan
Menyimpan absensi dan laporan perkembangan satu pertemuan untuk semua peserta dalam satu form.

**Content-Type:** `application/x-www-form-urlencoded` atau `application/json`

**Body:**

| Field | Type | Required | Keterangan |
|---|---|---|---|
| absensi | array | Ya | Array berisi data per peserta |

**Struktur array `absensi`:**

| Field | Type | Required | Keterangan |
|---|---tag |---|---|
| peserta_id | integer | Ya | ID peserta |
| status | string | Ya | `hadir`, `izin`, `sakit`, `alpa` |
| hal_mulai | integer | Tidak | Halaman mulai pertemuan (bisa null/kosong untuk izin/sakit/alpa) |
| hal_selesai | integer | Tidak | Halaman selesai pertemuan (bisa null/kosong untuk izin/sakit/alpa) |
| catatan_guru | string | Tidak | Catatan/laporan perkembangan untuk anak per sesi |

**Contoh Request (JSON):**

```json
{
  "absensi": [
    {
      "peserta_id": 10,
      "status": "hadir",
      "hal_mulai": 5,
      "hal_selesai": 10,
      "catatan_guru": "Sangat bagus, lancar membaca Al-Fatihah"
    },
    {
      "peserta_id": 11,
      "status": "hadir",
      "hal_mulai": 2,
      "hal_selesai": 5,
      "catatan_guru": "Perlu latihan makhraj huruf"
    },
    {
      "peserta_id": 12,
      "status": "sakit",
      "hal_mulai": null,
      "hal_selesai": null,
      "catatan_guru": "Izin karena sakit, jangan lupa ganti pertemuan"
    }
  ]
}
```

**Response Sukses:**

```json
{
  "success": true,
  "message": "Absensi dan laporan perkembangan berhasil disimpan",
  "data": null
}
```

### 5.7 Laporan Perkembangan (Alias)
Endpoint ini sama dengan `GET/POST /guru/absensi/{pertemuan_id}`. Disediakan untuk kemudahan naming di aplikasi mobile.

**Endpoint:** `GET /guru/laporan-perkembangan/{pertemuan_id}` | `POST /guru/laporan-perkembangan/{pertemuan_id}`  
**Auth:** Wajib + Guru aktif

---

## 6. Kode Error

| Kode | Keterangan | Penjelasan |
|---|---|---|
| 200 | OK | Berhasil |
| 201 | Created | Berhasil dibuat (register, buat pertemuan, upload bukti, daftar kelas) |
| 400 | Bad Request | Request tidak valid / data tidak lengkap |
| 401 | Unauthorized | Belum login atau token tidak valid/tidak ada |
| 403 | Forbidden | Tidak memiliki akses (bukan guru saat mengakses fitur guru, atau bukan pemilik data) |
| 404 | Not Found | Data tidak ditemukan |
| 405 | Method Not Allowed | Method HTTP tidak diizinkan |
| 422 | Unprocessable Entity | Validasi gagal (error detail ada di field `errors`) |
| 429 | Too Many Requests | Terlalu banyak percobag dern (rate limiting) | 
| 500 | Internal Server Error | Kesalahan server |
| 501 | Not Implemented | Belum diimplementasi |

---

## 7. Catatan Penting Untuk Pengembangan Mobile

### Deteksi Menu Guru
1. Setelah login, panggil `GET /me` untuk mendapatkan `is_guru`
2. Jika `is_guru === true` dan `guru.status === 'aktif'`, tampilkan tab/menu **"Guru"**
3. Jika `is_guru === false`, **JANGAN** tampilkan menu guru sama sekali
4. Semua endpoint `/guru/*` akan memblock jika user tidak terdaftar aktif sebagai guru dengan response `403`

### Halaman Sebelum Login
- Aplikasi mobile bisa menampilkan halaman **Beranda**, **Katalog Program**, **Detail Program**, **Detail Kelas** tanpa login
- **Hanya** saat user akan mendaftar kelas, upload bukti, atau mengakses fitur peserta/guru, baru diarahkan ke login/registrasi

### Transaksi
- Upload bukti harus menggunakan `multipart/form-data`
- Validasi file: JPG, JPEG, PNG, PDF, maksimal 2MB
- Bukti akan menunggu verifikasi admin di web. Status pembayaran bisa dilihat dari tagihan atau dashboard
- Untuk melihat bukti, gunakan URL `/api/pembayaran/bukti/{id}` dengan header `Authorization: Bearer <token>`

### Absensi & Laporan Perkembangan
- Satu form per pertemuan untuk semua peserta
- Bisa diisi bertahap (jika belum selesai bisa disimpan kembali, akan di-update)
- Untuk izin/sakit/alpa, field `hal_mulai` dan `hal_selesai` bisa dikosongkan/null
- `catatan_guru` bisa diisi untuk semua status kehadiran sebagai laporan perkembangan

### Role & Relasi
- **Guru sudah pasti peserta**: User bisa memiliki role berbeda, tapi deteksi guru berdasarkan tabel `guru`
- **Peserta tidak semua bisa jadi guru**: Harus ada proses penambahan di tabel `guru` oleh admin
- **Satu akun bisa menaungi banyak profil peserta**: Gunad path `profil_peserta` dari endpoint `/me` untuk switch antar anak

### Rate Limiting
- Login: 10 percobaan per IP per 15 menit
- Registrasi: 5 percobaan per IP per 60 menit

---

**Dokumentasi ini dibuat berdasarkan struktur database dan logic aplikasi LBQ Al-Utsmani.** Untuk pertanyaan lebih lanjut, silakan merujuk ke `AGENTS.md` dan `database.sql`.
