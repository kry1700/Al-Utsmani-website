# DEPLOY.md — Deploy ke Hosting (Production)

Panduan singkat memindahkan LMS LBQ Al-Utsmani dari XAMPP lokal ke hosting
(shared hosting/cPanel). Untuk penjelasan arsitektur & konvensi kode, lihat
`AGENTS.md`; untuk progres fitur, lihat `TASKS.md`.

> Aplikasi ini **tidak butuh Composer/vendor**. Yang di-upload adalah seluruh
> isi folder proyek: `application/`, `system/`, `assets/`, `uploads/`,
> `index.php`, `.htaccess`.

---

## 1. Prasyarat server

| Kebutuhan | Keterangan |
|---|---|
| PHP | Minimal 7.0 (kode memakai `random_bytes`, `password_hash`). Disarankan **8.1 / 8.2**. |
| Ekstensi PHP | `mysqli`, `session`, `json`, `fileinfo` (validasi upload), `mbstring` (opsional). |
| Database | MySQL 5.6+ / MariaDB 10.x. |
| Apache | `mod_rewrite` aktif dan `AllowOverride` mengizinkan `.htaccess`. |
| HTTPS | Disarankan aktif (SSL dari hosting). |

Jika hosting memakai **nginx** (bukan Apache), `.htaccess` diabaikan. Tambahkan
rewrite setara di config nginx, atau akses lewat `index.php/...`.

---

## 2. Langkah deploy

### 2.1 Buat database

1. Di cPanel: **MySQL Databases** → buat database + user, lalu **Add User to
   Database** dengan ALL PRIVILEGES.
2. Catat tiga nilai: **nama database**, **username**, **password** (di cPanel
   biasanya berprefix akun, mis. `karn3777_projek`).

### 2.2 Import `database.sql`

**Jangan langsung import apa adanya di shared hosting.** File ini diawali:

```sql
CREATE DATABASE IF NOT EXISTS al_utsmani_lms ...;
USE al_utsmani_lms;
```

User database shared hosting umumnya **tidak boleh** `CREATE DATABASE`, dan nama
database-nya bukan `al_utsmani_lms`. Kalau dibiarkan, import bisa berhenti dan
tabel tidak terbentuk.

Caranya:

1. Buka phpMyAdmin → **pilih database** yang tadi dibuat.
2. Tab **Import** → pilih `database.sql`.
3. Sebelum import, hapus/komentari dua baris `CREATE DATABASE ...` dan
   `USE ...` di bagian atas file (atau import lewat tab SQL tanpa keduanya).
4. Jalankan. Hasil yang benar: **16 tabel**.

Cek cepat:

```sql
SHOW TABLES;
-- harus 16 baris, termasuk: gelombang, pendaftaran_gelombang
SELECT COUNT(*) FROM gelombang;
```

> `gelombang` dan `pendaftaran_gelombang` adalah tabel fitur Gelombang. Kalau
> salah satunya tidak ada, halaman beranda akan error 500 di production
> (lihat bagian Troubleshooting).

### 2.3 `application/config/database.php`

```php
$db['default'] = array(
	'dsn'      => '',
	'hostname' => 'localhost',          // sesuai info hosting (kadang 127.0.0.1)
	'username' => 'USER_DATABASE',
	'password' => 'PASSWORD_DATABASE',
	'database' => 'NAMA_DATABASE',
	'dbdriver' => 'mysqli',
	// ...
);
```

### 2.4 `application/config/config.php`

```php
$config['base_url']   = 'https://domain-anda.com/'; // WAJIB https + trailing slash
$config['index_page'] = '';                          // URL bersih (butuh mod_rewrite)
```

Blok **"Penyesuaian otomatis untuk production"** di akhir file akan aktif
sendiri saat `ENVIRONMENT === 'production'`:

- `cookie_secure = TRUE` dan `cookie_httponly = TRUE`
- `log_threshold = 1` (error masuk ke `application/logs/`, tidak tampil ke
  pengunjung)

Tidak perlu diubah manual. Kalau ingin mematikan sementara, ubah kondisi blok
tersebut.

### 2.5 `index.php`

```php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
```

`production` menyembunyikan error dari pengunjung. Saat masih men-debug, boleh
sementara diubah ke `development`, lalu **kembalikan ke `production`** setelah
beres.

### 2.6 Upload file

Upload **seluruh** isi proyek ke `public_html` (atau subfolder — sesuaikan
`base_url`). Pastikan ikut ter-upload:

- `.htaccess` di root (file titik sering terlewat oleh FTP)
- folder `system/` (framework CodeIgniter, wajib)
- `assets/` dan `uploads/`
- `application/.htaccess` dan `uploads/bukti_pembayaran/.htaccess`

Folder `application/logs/` dan `application/cache/` harus ada dan **writable**.

### 2.7 Permission

| Path | Permission |
|---|---|
| Semua folder | `755` |
| Semua file | `644` |
| `application/cache/` | `755` / `775` (writable) |
| `application/logs/` | `755` / `775` (writable) |
| `uploads/bukti_pembayaran/` | `755` / `775` (writable) |

Di cPanel biasanya file yang di-upload sudah dimiliki user yang sama dengan
PHP, jadi `755` cukup.

---

## 3. Verifikasi setelah deploy

1. Buka `https://domain-anda.com/` → beranda tampil.
2. Cek halaman: `/katalog`, `/daftar`, `/gelombang`, `/auth/login`.
3. Login admin (`admin` / `admin123` dari seed) → **langsung ganti password**.
4. Uji satu alur: daftar kelas → upload bukti → verifikasi admin.
5. Pastikan `https://domain-anda.com/assets/img/logo-utsmani.jpg` tampil
   (bukti `.htaccess` & aset normal).

---

## 4. Troubleshooting error 500

Urutkan dari yang paling cepat:

### 4.1 Bedakan masalah server vs aplikasi

Buka URL aset statis, mis. `/assets/img/logo-utsmani.jpg`.

- **Aset juga 500** → masalah `.htaccess`/server, bukan PHP. Penyebab umum:
  `mod_rewrite` mati, `AllowOverride` tidak mengizinkan `.htaccess`, atau
  direktif Apache tidak didukung. Coba rename `.htaccess` → `.htaccess.bak`,
  lalu akses `/index.php`. Kalau normal, kembalikan `.htaccess` secara minimal
  atau minta hosting mengaktifkan `mod_rewrite`.
- **Aset normal, halaman PHP 500** → error aplikasi. Lanjut ke 4.2.

### 4.2 Tampilkan error aslinya

Di `index.php`, ubah sementara `production` → `development` (error tampil di
browser), atau set `log_threshold = 1` lalu cek `application/logs/` dan menu
**Errors** di cPanel. Kembalikan ke `production` setelah selesai.

### 4.3 Penyebab yang sering

| Gejala | Penyebab / solusi |
|---|---|
| `Table 'xxx.gelombang' doesn't exist` | Import DB lama/ tidak lengkap. `SHOW TABLES;` harus 16 tabel. Import ulang `database.sql` (tanpa `CREATE DATABASE`/`USE`). |
| Halaman blank 500, tanpa pesan | Di production error disembunyikan. Biasanya error DB (mis. tabel kurang) → `db_debug` mematikan pesan. Ubah ke `development` untuk melihatnya. |
| `Unable to connect to your database` | `hostname`/`username`/`password`/`database` salah, atau user belum di-attach ke DB. |
| `Call to a member function result() on bool` | Query gagal (tabel/kolom tidak ada) lalu hasil `FALSE` dipakai. Lihat pesan DB di mode development. |
| Login gagal / balik ke halaman login | Cookie `Secure` aktif tapi situs diakses via `http://` — pakai `https://`, atau sesuaikan `cookie_secure`. Bisa juga `sess_save_path` tidak writable. |
| Session error / tidak bisa login | Set `sess_save_path` ke folder absolut yang writable, mis. `sys_get_temp_dir()`, atau buat `application/cache/sessions` dan isi path-nya. |
| Error saat upload bukti | Folder `uploads/bukti_pembayaran/` tidak writable, atau `upload_max_filesize`/`post_max_size` PHP < 2 MB. |
| 403 pada POST | Normal jika halaman tidak punya form/CSRF token. Halaman berform sudah membawa token otomatis. |

### 4.4 Tabel wajib

`database.sql` harus menghasilkan 16 tabel:

```
absensi, api_tokens, cabang, gelombang, guru, kelas, level, pembayaran,
pendaftaran, pendaftaran_gelombang, penempatan_level, pertemuan, peserta,
program, tagihan_spp, users
```

---

## 5. Catatan keamanan

- Ganti password admin seed setelah login pertama.
- `uploads/bukti_pembayaran/` di-deny langsung; file hanya dilayani lewat
  controller yang memeriksa pemilik/admin.
- Redirect HTTP → HTTPS ada di `.htaccess` tetapi **dikomentari** (agar
  localhost tetap jalan). Di production, aktifkan blok `RewriteCond %{HTTPS}
  off` bila SSL sudah stabil.
- Jangan biarkan `ENVIRONMENT = development` di server publik.
- `database.sql`, `*.md`, `.env`, dan file sensitif lain diblokir `.htaccess`.
