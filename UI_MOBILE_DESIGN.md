# UI Mobile Design - LBQ Al-Utsmani

Dokumentasi desain UI mobile untuk aplikasi LBQ Al-Utsmani yang mengonsumsi REST API yang telah dibuat. Desain ini dibuat dengan mengacu pada tampilan website saat ini (Design System "Donezo" dengan rebrand coklat Caramel).

## 1. Tujuan Desain

- Dapat diakses sebelum login (beranda, katalog program, detail kelas)
- Wajib login jika ingin daftar kelas, membayar, absen, atau mengakses fitur khusus
- Pemisahan jelas antara mode **Peserta/Wali** dan **Guru**
- Menu **Guru** HANYA muncul jika user terdaftar aktif di tabel `guru` (dicek via `GET /api/me` dengan field `is_guru`)
- Fitur guru: Dashboard, Jadwal, Kelas, Daftar Peserta, Pertemuan, Absensi & Laporan Perkembangan per sesi
- Fitur peserta/wali: Dashboard, Kelas Saya, Daftar Kelas, Tagihan, Upload Bukti Pembayaran
- Satu akun bisa menaungi banyak profil peserta (bisa switch antar anak)
- Proses transaksi bisa dilakukan full dari mobile

## 2. Design System

Mengacu pada design system yang ada di website (`views/layouts/header.php`) dengan penyesuaian untuk layar mobile.

### 2.1 Warna

| Token | Hex | Kegunaan |
|---|---|---|
| `primary` | `#b98a56` / `#8c6239` (Caramel) | Tombol utama, active state |
| `primary-600` | `#8c6239` | Hover/pressed state |
| `canvas` | `#f7f5f3` | Background aplikasi |
| `surface` | `#ffffff` | Card, bottom nav, modal |
| `ink` | `#1f1b16` | Teks utama |
| `muted` | `#6b5d52` | Teks sekunder |
| `line` | `#e7dfd3` | Border, divider |
| `success` | `#22c55e` | Hadir, Lunas, Aktif |
| `warning` | `#f59e0b` | Menunggu, Belum Diisi |
| `danger` | `#ef4444` | Ditolak, Alpa, Error |
| `info` | `#3b82f6` | Izin |

### 2.2 Tipografi

| Elemen | Size (sp/dp) | Weight | Keterangan |
|---|---|---|---|
| `Heading 1` | 24-28 | 700-800 | Judul halaman |
| `Heading 2` | 20-22 | 700 | Section title |
| `Heading 3` | 18 | 600-700 | Card title |
| `Body` | 14-16 | 400-500 | Teks utama |
| `Caption` | 12-13 | 400-500 | Label, badge, metadata |
| `Button Text` | 14-16 | 600 | Teks tombol |

**Font Family:** `Plus Jakarta Sans`, `Inter`, `system-ui`, sans-serif

### 2.3 Spacing, Radius & Shadow

| Variabel | Nilai | Kegunaan |
|---|---|---|
| `radius-sm` | 8-10px | Input, badge kecil |
| `radius-md` | 12-14px | Button, list item |
| `radius-lg` | 16-20px | Card |
| `radius-xl` | 24px | Modal, bottom sheet |
| `spacing-xs` | 4-8px | Gap kecil |
| `spacing-sm` | 8-12px | Padding elemen |
| `spacing-md` | 12-16px | Gap antar elemen |
| `spacing-lg` | 16-24px | Padding card/section |
| `shadow-sm` | `0 1px 2px rgba(31,27,22,0.02)` | Subtle |
| `shadow-md` | `0 8px 24px rgba(140,98,57,0.08)` | Card default |
| `shadow-lg` | `0 12px 40px rgba(140,98,57,0.12)` | Elevated card/modal |

## 3. Struktur Navigasi

### 3.1 Mode Tamu (Belum Login)
Dapat mengakses beranda, katalog, detail program/kelas tanpa autentikasi.

```text
Bottom Navigation:
[Beranda] [Katalog] [Login]
```

| Route/Screen | Deskripsi |
|---|---|
| `HomeGuest` | Landing page, hero, program unggulan, cabang, CTA |
| `Catalog` | Daftar program (BIHAR, BADAL, BILAL, PDHQ) |
| `ProgramDetail` | Daftar kelas per program + filter cabang/mode |
| `ClassDetail` | Detail kelas lengkap + sticky button "Dafta# PR Title
```
# api documentation

## Overview
- base url: http://localhost/al-utsmani/api
- content-type: application/json (for json body), multipart/form-data (for upload)
- response format: `{"success": true|false, "message": "...", "data": {...}|null, "errors": {...}|null}`
- auth: bearer token via header `authorization: bearer <token>` (token from login, expires 30 days)
- cors: enabled
- rate limiting: login 10/ip/15 min, register 5/ip/60 min
- uploads: `uploads/bukti_pembayaran/` protected (access via controller/api)

## Auth

### post /auth/login
login with identifier (email or username) + password.
- body: `{"identifier": "...", "password": "..."}`
- 200: `{"success": true, "message": "login berhasil", "data": {"token": "...", "expires_at": "yyyy-mm-dd hh:mm:ss", "user": {"id": 5, "nama": "...", "username": "...", "email": "...", "role": "peserta"}}}`
- 401/403/429 on error

### post /auth/register
register akun peserta/wali (creates user + first peserta profile)
- body: `{"nama": "...", "username": "...", "email": "...", "no_wa": "...", "password": "...", "konfirmasi_password": "..."}`
- validations: username regex `/^[a-zA-Z0-9_.]+$/`, unique username/email, no_wa regex, min 8 password, match
- 201: `{"success": true, "message": "registrasi berhasil. silakan login.", "data": {"peserta_id": 10}}`

### post /auth/logout
- auth required
- deletes token from api_tokens
- 200: `{"success": true, "message": "logout berhasil", "data": null}`

### get /me
auth required. used to determine ui (is_guru, list profil_peserta)
- 200: 
```json
{
  "success": true,
  "message": "data user berhasil diambil",
  "data": {
    "user": {"id": 5, "nama": "...", "username": "...", "email": "...", "role": "peserta", "status": "aktif"},
    "is_guru": true|false,
    "guru": {"id": 2, "nama": "...", "no_wa": "...", "status": "aktif"} | null,
    "profil_peserta": [{"id": 10, "nama": "...", "tanggal_lahir": "...", "jenis_kelamin": "L|P", "level_id": 1|null, "level_nama": "...|null"}]
  }
}
```
note: is_guru is true only if record exists in `guru` with status aktif (do not rely solely on user.role)

## Public (no auth)

### get /beranda
landing data
- 200: `{"success": true, "data": {"programs": [{"id": 1, "kode": "...", "nama": "...", "deskripsi": "...", "urutan": 0, "jumlah_kelas": 5}], "cabangs": [{"id": 1, "wilayah": "...", "nama": "..."}]}}`

### get /katalog/program
list program + rekap
- 200: `{"success": true, "data": [{"id": 1, "kode": "...", "nama": "...", "deskripsi": "...", "urutan": 0, "jumlah_kelas": 5, "cabang": ["..."]}]}`

### get /katalog/program/{id}
classes per program
- 200: `{"success": true, "data": {"program": {"id": 1, "kode": "...", "nama": "...", "deskripsi": "..."}, "kelas": [{"id": 10, "program_kode": "...", "program_nama": "...", "level_nama": "...", "level_urutan": 1, "cabang_nama": "...", "cabang_wilayah": "...", "mode": "offline|online", "jadwal": "...", "zona_waktu": "WIB|WITA|WIT", "biaya_pendaftaran": 0, "biaya_spp": 150000, "guru_nama": "..."}]}}`

### get /katalog/kelas/{id}
class detail
- 200: `{"success": true, "data": {"id": 10, "program": {"kode": "...", "nama": "..."}, "level": {"nama": "..."}, "cabang": {"nama": "...", "wilayah": "..."}, "guru": {"nama": "..."}, "mode": "offline|online", "jadwal": "...", "zona_waktu": "WIB|WITA|WIT", "link_meeting": "...|null", "biaya_pendaftaran": 0, "biaya_spp": 150000, "tipe_peserta": "private|kelompok|perusahaan|instansi|null", "nama_instansi": "...|null", "status": "aktif|nonaktif", "jumlah_peserta_aktif": 12}}`

## Peserta/Wali (auth required)

### get /peserta/dashboard
- 200: `{"success": true, "data": {"profil_peserta": [...], "pendaftaran": [{"id": 25, "kelas_id": 10, "peserta_id": 10, "status": "aktif|menunggu|ditolak|batal", "jadwal": "...", "mode": "offline|online", "link_meeting": "...|null", "program_nama": "...", "program_kode": "...", "level_nama": "...", "cabang_nama": "..."}], "tagihan": [{"id": 15, "peserta_id": 10, "periode": "yyyy-mm", "nominal": 150000, "jatuh_tempo": "yyyy-mm-dd", "status": "belum|lunas"}], "jumlah_tagihan_belum_lunas": 1}}`

### get /peserta/tagihan
- 200: list of tagihan with `peserta_nama`

### post /peserta/daftar-kelas
register to class. biaya_pendaftaran 0 -> status aktif, else menunggu
- body: `{"kelas_id": 10, "peserta_id": 10}`
- 201: `{"success": true, "message": "...", "data": {"pendaftaran_id": 25, "status": "aktif|menunggu", "perlu_bayar_pendaftaran": true|false, "biaya_pendaftaran": 100000}}`
- 400 if already registered/inactive class

## Pembayaran (auth required)

### post /pembayaran/upload-bukti
multipart/form-data
- fields: `jenis` (pendaftaran|spp), `peserta_id`, `jumlah`, `bukti` (file), `pendaftaran_id` (if pendaftaran), `tagihan_spp_id` (if spp)
- validation: jpg/jpeg/png/pdf, <= 2mb (via helper validasi_bukti), encrypt_name true
- 201: `{"success": true, "message": "bukti pembayaran berhasil diunggah, menunggu verifikasi admin", "data": {"pembayaran_id": 30, "bukti_file": "...", "status": "menunggu"}}`

### get /pembayaran/bukti/{id}
returns file directly (inline for images, attachment for pdf). must be owner or admin. auth required.
- headers: content-type, content-length, content-disposition, cache-control: private, no-store

## Guru (auth required + must be active guru)

checked via `requireGuru()` - looks up `guru` by user_id and status aktif. endpoint blocked 403 otherwise.

### get /guru/dashboard
- 200: `{"success": true, "data": {"guru": {"id": 2, "nama": "..."}, "jumlah_kelas": 2, "jumlah_pertemuan": 15, "jumlah_peserta": 24, "kelas": [...]}}`

### get /guru/jadwal
list kelas aktif diampu

### get /guru/kelas
list all kelas diampu with `jumlah_peserta`, `jumlah_pertemuan`

### get /guru/kelas/{id}/peserta
peserta aktif per kelas + level (level_id/nama/urutan null if belum ditempatkan)
- 403 if class not owned by guru

### get/post /guru/pertemuan/{kelas_id}
get: list pertemuan with `terisi` (count absensi entries)
post: create pertemuan (auto nomor next). body: `{"tanggal": "yyyy-mm-dd"}`
- 403 if class not owned

### get/post /guru/absensi/{pertemuan_id}
get: form with pertemuan info + list peserta with prefill absensi
post: save absensi + laporan perkembangan (bulk). body: `{"absensi": [{"peserta_id": 10, "status": "hadir|izin|sakit|alpa", "hal_mulai": 5|null, "hal_selesai": 10|null, "catatan_guru": "..."}]}`
uses `Absensi_model::simpanBulk()` (insert/update per peserta per pertemuan)
- 403 if pertemuan not owned by guru

### get/post /guru/laporan-perkembangan/{pertemuan_id}
alias of /guru/absensi/{pertemuan_id}

## Errors
- 400 bad request
- 401 unauthorized
- 403 forbidden
- 404 not found
- 405 method not allowed
- 422 validation errors (errors object)
- 429 rate limited
- 500 server error

## Notes
- for mobile menu: call `/me` after login; show guru tab only if `is_guru === true` and guru.status aktif
- guest can view beranda/katalog/detail kelas; login required for daftar kelas, upload bukti, guru features
- upload bukti only via api/controller (no direct link to uploads)
- laporan perkembangan stored in `absensi` (hal_mulai, hal_selesai, catatan_guru) per pertemuan per peserta
- transaksi verification remains on admin web (like existing flow)
