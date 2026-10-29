# TASKS.md — LBQ Al-Utsmani

Task management utama proyek. Centang setelah selesai. Panduan teknis: lihat `AGENTS.md`.

Status: [x] Fase 1, [x] Fase 2, [x] Fase 3, [x] Fase 4 — **selesai & sudah diuji end-to-end** (smoke test curl + verifikasi DB). [x] Fase 5 — **redesign sisi peserta, selesai & teruji** (smoke test curl + verifikasi DB). [x] Fase 6 — **kartu program bergambar + daftar kelas/jam, selesai & teruji** (smoke test curl + verifikasi DB + simulasi logika JS). [x] Fase 7 — **design system "Donezo" (token global + panel admin bersidebar), selesai & teruji** (php -l + smoke test curl semua halaman admin, tanpa error PHP). [x] Fase 8 — **poles komponen Donezo ke halaman guru, peserta, & publik, selesai & teruji** (php -l + smoke test curl semua halaman role/publik, tanpa error PHP); menunggu konfirmasi testing manual user. [x] Fase 9 — **rebrand "LBQ Al-Utsmani" + tema coklat Caramel + logo + hero baru, selesai & teruji** (php -l + smoke test curl publik & admin, tanpa error PHP). [x] Fase 10 — **hero full-bleed 70vh (teks kiri + gambar kanan) + kartu program diperbesar, selesai & menunggu verifikasi manual user** (php -l + smoke test curl beranda/daftar/katalog/admin, DOM seimbang, tanpa error PHP). [x] Fase 11 — **bar ikon pintasan + navbar tamu center + tagline hero, selesai & menunggu verifikasi manual user** (php -l 111/111 + smoke test curl tamu/admin, DOM seimbang, tanpa error PHP). [x] Fase 12 — **hari kelas + gelombang "Coming Soon" + hero full-screen slider, selesai & teruji** (php -l + smoke test curl + pengukuran geometri DOM via Chrome headless, tanpa error PHP).

## Fase 1 — Fondasi

- [x] `database.sql`: seluruh struktur tabel (FK + index + unique) + seed level, program, 12 cabang, 1 akun admin
- [x] Konfigurasi: `base_url` → `http://localhost/Al-Utsmani/`, `database.php` (DB `al_utsmani_lms`), aktifkan CSRF, timezone `Asia/Jakarta`, autoload library/helper dasar
- [x] `MY_Controller` (session user + helper dasar) + `Admin_Controller` / `Guru_Controller` / `Peserta_Controller` (guard role)
- [x] Helper: `format_helper` (rupiah, tanggal, periode), `auth_helper` + rate limiting sederhana (CI cache)
- [x] Layout Tailwind (header/nav responsif, konten per halaman) — publik + per role
- [x] Autentikasi: Login (field Email atau Username), Registrasi peserta/wali, Logout, password_hash
- [x] Data master cabang (CRUD + wilayah + status)
- [x] Data guru (CRUD: buat akun user + profil guru + status)
- [x] Data kelas (CRUD: program, level, cabang, mode, guru, jadwal+ZI, link meeting, biaya, status; validasi PDHQ hanya Bogor+offline; BILAL wajib tipe peserta)

## Fase 2 — Pendaftaran dan pembayaran

- [x] Katalog kelas publik + filter (program, level, cabang, mode) — hanya kelas aktif *(Fase 5: katalog diubah menjadi carousel program, filter level dihapus — lihat Fase 5)*
- [x] Detail kelas publik (info lengkap; tombol daftar untuk peserta login)
- [x] Pendaftaran kelas oleh peserta (pilih profil peserta/anak; biaya 0 → langsung aktif; UNIQUE per kelas)
- [x] Upload bukti pendaftaran (JPG/PNG/PDF, maks 2 MB, nama acak, server-side validation)
- [x] Akses file bukti hanya via controller (cek pemilik/admin), `uploads/` deny-all
- [x] Verifikasi/tolak bukti oleh admin (transaksi: pembayaran + status pendaftaran; tolak → boleh upload ulang)
- [x] Data peserta: halaman admin (daftar, detail berisi riwayat pendaftaran/pembayaran, CRUD akun+profil)
- [x] Halaman Profil & Data Peserta (peserta/wali: kelola beberapa profil anak, edit data)

## Fase 3 — Pembelajaran

- [x] Pertemuan per kelas (dibuat guru; UNIQUE kelas+nomor)
- [x] Form absensi + progres per pertemuan (satu form, satu simpan transaksi, bisa diedit ulang)
- [x] Riwayat pertemuan per kelas (guru: mana yang sudah diisi)
- [x] Dashboard guru: kelas yang diampu + daftar peserta
- [x] Riwayat absensi (peserta, pilih profil anak) & Riwayat progres (hal. mulai–selesai per pertemuan)
- [x] Link meeting online hanya tampil untuk peserta terdaftar di kelas itu + guru pengampu

## Fase 4 — SPP dan dashboard

- [x] Tagihan SPP bulanan (nominal manual admin, prefill SPP kelas; `UNIQUE(peserta_id, periode)`; jatuh tempo default tgl 10)
- [x] Upload + verifikasi bukti SPP (transaksi: pembayaran → tagihan lunas)
- [x] Halaman tunggakan (belum lunas + lewat jatuh tempo): jumlah peserta + total nominal
- [x] Dashboard admin: filter periode + cabang — peserta baru/lama/total, total guru, kelas aktif, cabang aktif, pemasukan (total + per cabang), tunggakan
- [x] Perkembangan per cabang (rekap realtime per cabang: peserta aktif, program aktif, kelas aktif, guru, pemasukan, tunggakan) + drill-down detail peserta aktif
- [x] Ringkasan hasil + panduan testing manual per fase → konfirmasi user

## Fase 5 — Redesign sisi peserta (beranda, wizard pendaftaran, carousel, penempatan level)

- [x] Skema: `peserta.level_id` (+ `level_ditetapkan_at`, `level_ditetapkan_by`) dan tabel riwayat `penempatan_level` (level jadi milik peserta, ditetapkan pengelola)
- [x] `Peserta_model` dirombak (join level, `daftar($q, $filterLevel)`, `countBelumLevel`) + `Penempatan_model` baru (riwayat + penetapan dalam satu transaksi)
- [x] Halaman admin **Penempatan Level**: daftar peserta (cari + filter semua/belum/level) dengan badge level, form tetapkan level + catatan, riwayat penempatan; kartu riwayat di detail peserta; metrik "peserta belum ditempatkan" + pintasan di dashboard admin
- [x] Identitas level peserta (title profil singkat): helper `badge_level()`; kartu "Profil Singkat" di dashboard peserta (nama · level · program · cabang · jumlah kelas aktif, badge abu "Belum ada level" + info menunggu penempatan) dan badge pada halaman Profil
- [x] Beranda dirombak: hero (gambar stok + penjelasan Al-Utsmani) → statistik → **pemilih dua arah** (Cabang→Program **atau** Program→Cabang, difilter sisi klien) → carousel program → "Cara Mendaftar" + catatan level
- [x] Alur pendaftaran terpadu `Daftar` (`/daftar`, `/daftar/kelas`, `/daftar/registrasi/<kelas_id>`): tamu membuat **akun + profil + pendaftaran** sekaligus (bukti pembayaran boleh langsung diunggah, opsional); peserta login cukup pilih profil anak
- [x] Katalog jadi **carousel program** (`views/katalog/carousel.php`, scroll-snap + panah + dot + swipe, tanpa autoplay) + halaman `katalog/program/<id>` (filter cabang + mode, tanpa filter level); tombol daftar di detail kelas diarahkan ke alur terpadu
- [x] Aset gambar stok berlisensi bebas (Wikimedia Commons) diunduh lokal ke `assets/img/` (hero + 4 program) dengan fallback gradient + kode program bila berkas tidak ada; kredit lisensi di `assets/img/CREDITS.md`
- [x] Route/nav: link "Penempatan" (admin) dan "Daftar Kelas" (peserta) di navbar; `helpers/format_helper.php` + `program_gambar()`/`hero_gambar()`
- [x] Uji: `php -l` semua berkas, smoke test HTTP (publik + admin + peserta), verifikasi DB (registrasi publik → akun/profil/pendaftaran/pembayaran; penempatan level → riwayat + level terkini; daftar kelas biaya 0 → langsung aktif; guard profil bukan milik sendiri → 403)

## Catatan implementasi (hasil pengujian)

- Semua halaman lolos `php -l`; smoke test curl: halaman publik + seluruh halaman admin/guru/peserta = 200 tanpa error PHP.
- Guard role: peserta→admin/guru = 403, admin→peserta = 403, anonim→admin = 307 (redirect login). Kelas/pertemuan/absensi milik guru lain = 403.
- File bukti: akses langsung HTTP = 403 (.htaccess), lewat controller hanya pemilik/admin, guru lain = 403.
- Rate limit (login 10/15 mnt, registrasi 5/60 mnt, unggah 20/60 mnt) TERBUKTI aktif (uji berulang → diblokir + pesan flash).
- Verifikasi: pendaftaran berbiaya → `menunggu` lalu `aktif`; tolak → `ditolak` + catatan, unggah ulang menimpa file lama (file lama dihapus); SPP terverifikasi → tagihan `lunas` + `lunas_at`.
- Dashboard: pemasukan dihitung dari tanggal verifikasi, per cabang mengikuti kelas (SPP memakai cabang pendaftaran aktif terlama) — sudah dicocokkan dengan data uji (Rp 450.000 = 150k + 50k + 250k; Condet 400k, Dukuh 50k).
- Status nonaktif dipakai di cabang/guru/kelas; untuk peserta, tombol di halaman detail berbunyi "Nonaktifkan **Akun**" karena status ada di tabel `users` (satu akun bisa punya banyak profil).

### Fase 5 (redesign sisi peserta) — hasil uji

- Halaman publik 200: `/`, `/beranda`, `/katalog`, `/katalog/program/1`, `/katalog/detail/1`, `/daftar`, `/daftar/kelas?program=1`, `/daftar/kelas?cabang=1`, `/daftar/registrasi/2`, `/auth/login`, `/auth/registrasi`.
- Registrasi publik dari wizard (POST `/daftar/registrasi/2` + unggah bukti) → 1 transaksi: `users` (role peserta, aktif), `peserta` (level_id NULL = belum ditempatkan), `pendaftaran` `menunggu`, `pembayaran` `menunggu` dengan nama berkas acak. Flash: "Akun berhasil dibuat. … menunggu verifikasi admin."
- Penempatan level (admin): `POST /admin/penempatan/simpan/1` → `peserta.level_id` + `level_ditetapkan_at/by` terisi **dan** satu baris riwayat `penempatan_level` (catatan tampil di halaman form + kartu riwayat detail peserta).
- Peserta login: dashboard menampilkan kartu "Profil Singkat" dengan badge level (Level 1) + tombol "Daftar Kelas Baru"; halaman Profil menampilkan badge level.
- Peserta login mendaftar kelas biaya 0 (`/daftar/registrasi/1`, `peserta_id=1`) → `pendaftaran.status` langsung `aktif`.
- Guard: peserta memakai `peserta_id` yang bukan miliknya → **403 "Akses Ditolak"**; guru mengirim POST ke alur pendaftaran ditolak CSRF (halaman tanpa form → tanpa token; artefak uji, bukan bug); `/daftar/kelas` tanpa parameter → 307 redirect ke `/daftar` + pesan.
- Katalog tidak lagi punya filter `level` (dicek: tidak ada elemen `name="level"`).
- Gambar: Wikimedia Commons membatasi lebar thumbnail (hanya beberapa ukuran yang diizinkan; 500/960/1280 terbukti jalan) — dipakai 1280 px untuk hero dan 960 px untuk program (≤ ~280 KB/berkas). Server XAMPP ini **tidak punya GD/ImageMagick**, jadi ukuran dijaga dari sisi unduhan, bukan proses ulang.

## Fase 6 — Kartu program bergambar + daftar kelas & jam

- [x] `Kelas_model::rekapProgram()` diperluas: tiap program menyimpan `kelas` = daftar kelas aktif (`kelas_id, cabang_id, cabang, wilayah, level, urutan, jadwal, zona, mode`) dalam **satu query** (urut level → wilayah → cabang). Kunci `jumlah` & `cabang` tetap ada (kompatibel).
- [x] Kartu program jadi **kartu bergambar**: gambar utama (fallback gradient + kode) → badge kode (BIHAR) → nama program → daftar **kelas & jam** (Level · jadwal zona · cabang + chip Online/Offline), maks 3 baris + "+N kelas lainnya". Seluruh kartu = tombol pilih.
- [x] Dropdown "Pilih Program" (alur Program→Cabang) dan "Pilih Cabang" (alur Cabang→Program) diganti **kartu bergambar**; cabang dikelompokkan per wilayah.
- [x] Kartu cabang bergambar foto **per wilayah** (helper `cabang_gambar()`, fallback gradient + singkatan nama).
- [x] JS pemilih: item "sekunder" saja yang difilter (kartu utama tetap tampil); saat cabang dipilih (Cabang→Program) daftar kelas/jam tiap kartu program **di-render ulang** dari payload JSON (`<script id="data-pilih">`), tanpa JS tetap tampil semua.
- [x] Carousel program (beranda & `/katalog`) ikut menampilkan daftar kelas & jam dengan gaya sama (maks 3 baris).
- [x] Partial baru: `views/daftar/_kartu_program.php` (butuh `$p, $rekap, $maksBaris`) & `views/daftar/_kartu_cabang.php` (butuh `$c`); helper `baris_kelas()`, `baris_kelas_html()`, `cabang_gambar()`, `singkatan()`.
- [x] Aset: 6 foto wilayah (Wikimedia Commons, 960 px) → `assets/img/cabang-*.jpg` + kredit di `CREDITS.md`.
- [x] Uji: `php -l` bersih; `/`, `/daftar`, `/katalog` = 200 tanpa error; baris kelas & chip mode benar; cap "3 + N kelas lainnya"; JSON payload per-cabang/per-program tervalidasi; JS lolos `node --check`; fallback gambar diuji dengan menghapus berkas. DB dikembalikan ke instalasi bersih setelah uji.

## Fase 7 — Design system "Donezo" (token global + panel admin bersidebar)

Panduan sumber: `design-system.md` (tema Donezo). Keputusan: token diterapkan **global** (semua role), navigasi admin jadi **sidebar melayang**, dan komponen admin dipoles **menyeluruh**.

- [x] **Token global** (didefinisikan sekali di `views/layouts/header.php` lewat `tailwind.config`): warna `primary` (forest green `#1E5A3E`, `light #3DBB7A`, `soft #EAF5EF`, `dark #16442E`), `canvas #F4F6F5`, `ink`, `muted`, `line`; **peta ulang** skala `emerald`/`teal` → palet Forest Green dan `slate` → netral sejuk, sehingga seluruh kelas lama (`bg-emerald-600`, `text-slate-500`, dst.) otomatis ikut brand tanpa mengubah tiap view; font **Plus Jakarta Sans** (Google Fonts); `borderRadius` (lg 16px/xl 12px/2xl 20px) & `boxShadow` (`card`/`float`/`glow`).
- [x] **Kelas komponen** (`@layer components` di header): `card`, `card-head`, `card-body`, `page-title`, `btn`/`btn-primary`/`btn-outline`/`btn-ghost`/`btn-danger`/`btn-sm`/`btn-icon` (pill), `input`, `label`, `error`, `badge`, `stat-card`, `th`, `td`, `nav-item`/`nav-item-active`.
- [x] **Navigasi**: admin → **sidebar melayang** kiri (desktop, ikon+label, item aktif pill hijau) + **drawer** mobile (tombol hamburger, overlay, tutup via Esc/overlay) + top bar (pencarian peserta + user + keluar); partial item nav `views/layouts/_nav_admin.php` dipakai bersama sidebar & drawer. Role lain & publik → navbar atas di-restyle jadi **floating pill bar**. Flash message & footer ikut di-restyle; `.error` (form_error CI3) kini tampil rapi.
- [x] **Poles admin menyeluruh (17 view)**: dashboard (stat card berikon + kartu aksen gelap "Pemasukan" + tabel/pintasan seragam), semua halaman list (peserta, guru, kelas, cabang, pembayaran, tagihan, penempatan, perkembangan, perkembangan detail) pakai `card` + tabel `th`/`td` + hover baris, semua form (peserta, guru, kelas, cabang, penempatan, tagihan buat) pakai `input`/`label`/`btn-*`, badge status tetap lewat helper `badge()`/`badge_level()` (warnanya ikut token).
- [x] Uji: `php -l` bersih (semua `views/admin/*` + `views/layouts/*`); smoke test curl login admin → dashboard **303 → 200**, **14 halaman admin 200 tanpa error PHP**; struktur DOM seimbang (`<div>`/`</main>` seimbang, item nav aktif benar); uji halaman berdata (detail peserta, form penempatan, filter "belum ditempatkan") → 200, lalu data uji dihapus (DB kembali instalasi bersih).

## Fase 8 — Poles komponen "Donezo" ke halaman guru, peserta, & publik

Melanjutkan Fase 7: kelas komponen yang sama (`card`/`card-head`/`card-body`/`page-title`/`btn-*`/`input`/`label`/`badge`/`stat-card`/`th`/`td`) diterapkan ke view guru, peserta, dan publik/auth. Warna & font sudah global sejak Fase 7, jadi ini murni penyeragaman komponen (tanpa mengubah logika PHP/JS).

- [x] **Guru (5 view)**: `dashboard` (stat-card + tabel kelas), `kelas` (kartu + `btn-primary btn-sm`), `kelas_detail` (card, tabel pertemuan, form "Tambah Pertemuan" baris `bg-canvas`, daftar peserta aktif), `absensi_form` (tabel input absensi/progres), `absensi_riwayat` (tabel riwayat).
- [x] **Peserta (7 view + partial)**: `dashboard` (stat-card + kartu profil + tabel kelas & tagihan), `pendaftaran` (kartu + status bukti + `details` unggah), `pembayaran` (kartu tagihan + riwayat tabel + `details` unggah), `profil` & `profil_form` (kartu/grid + `input`/`label`), `riwayat_absensi` & `riwayat_progres` (tabel), `_pilih_profil` (filter kartu).
- [x] **Publik/auth konservatif**: `beranda` (hero dibiarkan — tombol di atas latar hijau/gambar tetap kustom; statistik jadi `stat-card`; bagian "Cara Mendaftar" jadi `card`), `auth/login` & `auth/registrasi` (kartu form terpusat), `katalog/index` & `katalog/program` & `katalog/detail` (judul `page-title`, filter `card`, kartu kelas, badge program/mode), `katalog/carousel` (kartu & tombol carousel), `daftar/pilih` & `daftar/kelas` & `daftar/registrasi` (judul, filter, kartu, `input`/`label`, CTA), `daftar/_pilih` (chrome: tab pill, `input`, `btn-primary`), `daftar/_kartu_program` & `daftar/_kartu_cabang` (border `line`, teks `ink`; gradient/fallback & kelas `peer-checked` dipertahankan).
- [x] JS tetap utuh: kelas yang di-toggle JS (`bg-emerald-600`, `text-slate-600`, `bg-slate-300`) sengaja dibiarkan karena skala `emerald`/`slate` sudah dipetakan ke token brand.
- [x] Uji: `php -l` 24 berkas view bersih; smoke test curl — publik (`/`, `/katalog`, `/katalog/program/1`, `/daftar`, `/daftar/kelas?…`, `/auth/login`, `/auth/registrasi`), guru (dashboard, kelas, kelas detail, absensi isi/riwayat), peserta (dashboard, pendaftaran, pembayaran, profil, form profil, riwayat absensi/progres) **semua 200 tanpa error PHP**; DOM seimbang; link meeting tampil untuk peserta terdaftar aktif; data uji dihapus (DB kembali bersih: 1 admin).
- [x] **Perbaikan bug (layout admin bocor ke halaman publik)**: `$isiAdmin` sebelumnya hanya melihat role, sehingga admin yang membuka beranda/katalog tetap mendapat sidebar admin (dan nav publik menampilkan menu peserta). Kini `$isiAdmin` mensyaratkan segmen URL pertama = `admin` (disamakan di `header.php` & `footer.php`), dan admin di halaman publik memakai **nav publik** dengan tautan "Panel Admin". Uji: admin login → `/`, `/katalog` tanpa sidebar/top bar admin (nav publik tampil); `/admin/dashboard`, `/admin/peserta` tetap sidebar; DOM seimbang, tanpa error PHP.

## Fase 9 — Rebrand "LBQ Al-Utsmani" + tema coklat Caramel + logo + hero baru

Permintaan user: (1) nama aplikasi "Al-Utsmani LMS" → **"LBQ Al-Utsmani"**; (2) warna brand hijau → **coklat** (nuansa Caramel/Gold-brown); (3) terapkan file logo lembaga; (4) ganti isi hero beranda.

- [x] **Rename**: `views/layouts/header.php` (`<title>` + alt logo), `views/layouts/footer.php` (kredit), `AGENTS.md`, `TASKS.md`, `design-system.md`, komentar `database.sql`. (`base_url`/path folder `Al-Utsmani` sengaja tidak diubah.)
- [x] **Tema coklat Caramel/Gold-brown**: token `primary` (`#8B5E34` / light `#C89B6A` / soft `#F7EFE3` / dark `#6B4423` / deep `#3F2915`), `canvas #FAF6F0`, `ink #1F1A16`, `muted #7A6A5A`, `line #EDE3D7`, peta ulang `emerald` & `teal` → gradasi krem→coklat, `boxShadow.glow` → `rgba(139,94,52,.25)`. Berlaku global (semua view ikut otomatis lewat pemetaan skala). `design-system.md` palet diselaraskan + catatan rebrand.
- [x] **Logo**: `img/utsmani.jpg` dipindah ke `assets/img/logo-utsmani.jpg` (folder root `img/` dihapus, sesuai konvensi gambar publik di `assets/img/`); helper baru **`logo()`** di `format_helper.php` (NULL bila berkas tidak ada → fallback badge "AU" tetap ada); logo dipasang di sidebar admin, drawer admin, top bar mobile admin, dan pill nav publik; **favicon** memakai logo.
- [x] **Hero beranda** (`views/beranda.php`): badge "LBQ Al-Utsmani"; H1 **"Yuk, Ngaji Bareng Al-Utsmani!"**; paragraf baru (metode Al-Utsmani sejak 1990); tombol **"Temukan Program"** → `/katalog` dan **"Pelajari Metode Kami"** → `#metode`; tautan "Sudah punya akun? Login" tetap untuk tamu. Section baru **"Metode Kami"** (`id="metode"`) berisi 5 poin keunggulan (ikon centang, grid responsif). Section statistik/pemilih/carousel/cara mendaftar dipertahankan.
- [x] Uji: `php -l` bersih; smoke test curl (publik & login admin) 200 tanpa error PHP; DOM seimbang; `assets/img/logo-utsmani.jpg` tersaji 200.

## Fase 10 — Hero besar full-bleed + kartu program diperbesar

Permintaan user: meniru **ukuran hero** situs referensi (FDC Dental Clinic: hero setinggi layar, judul ~72–96px) — dipilih versi proporsional (**70vh**), **full-bleed**, tata letak **teks kiri + gambar kanan**, sekaligus memperbesar **section program/kartu**. Navbar, font (Plus Jakarta Sans), dan palet coklat sengaja **tidak** diubah.

- [x] **Layout `main_penuh`** (`views/layouts/header.php` + `controllers/Beranda.php`): halaman bisa melepas batas `max-w-6xl` lewat variabel `$main_penuh` (dipakai hanya beranda → `<main class="w-full">`); flash message dibungkus wadah ber-padding bila `main_penuh` aktif. Halaman lain (katalog, auth, daftar, guru, peserta, admin) tidak berubah.
- [x] **Hero beranda** (`views/beranda.php`): section **full-bleed** (menyentuh tepi layar) dengan latar gradien krem→coklat + blob dekoratif; grid 2 kolom `md:min-h-[60vh] lg:min-h-[70vh]`; H1 `text-4xl → sm:text-5xl → lg:text-6xl`; paragraf & tombol (`btn-primary` "Temukan Program", `btn-outline` "Pelajari Metode Kami") + tautan login; kolom kanan foto `hero.jpg` (`aspect-[4/3]`, `rounded-3xl`, `shadow-float`), fallback kotak gradien "AU" bila gambar tidak ada. Section lainnya dibungkus `mx-auto max-w-6xl px-4 sm:px-6` dengan `space-y-10`.
- [x] **Kartu program diperbesar**: `katalog/carousel.php` gambar `h-40 → h-52`, judul `text-lg font-bold`, padding `p-5 → p-6`, tombol panah `h-10 w-10`; `daftar/_kartu_program.php` gambar `h-28 → h-36`, nama `text-base`, padding `p-3 → p-4` (partial ini dipakai bersama beranda & `/daftar`). Judul section beranda (`Metode Kami`, `Daftar Kelas`, `Program Kami`) naik ke `text-2xl sm:text-3xl`.
- [x] Uji: `php -l` 5 berkas bersih; smoke test curl `/beranda` (200, `<main class="w-full">`, hero 70vh tampil), `/daftar` (200, kartu `h-36`), `/katalog` (200, carousel `h-52`), login admin + `/admin/dashboard` (200, `<main>` admin **tidak** berubah), admin buka `/beranda` tetap full-bleed; DOM seimbang (243/243), tidak ada `w-screen` (bebas scroll horizontal), tanpa error PHP.

## Fase 11 — Bar ikon pintasan + navbar tamu center + tagline hero

Permintaan user: (1) meniru bar **ikon pintasan** pada situs referensi (Promo/Reservasi/Pricelist/Klinik Terdekat); (2) **Beranda & Katalog Kelas di tengah** navbar; (3) paragraf panjang hero diganti **"Belajar Quran, Mudah dan Menyenangkan"**.

- [x] **Bar ikon pintasan** (`views/beranda.php`): kartu putih melayang menimpa batas bawah hero (`relative z-10 -mt-8 sm:-mt-10`, `max-w-4xl`), grid 2 kolom (HP) / 4 kolom (`sm+`). Tiap item: lingkaran ikon `bg-primary-soft text-primary` → hover `bg-primary text-white`, label di bawah. Isi & tujuan: **Program** → `/katalog` (book-open), **Daftar Kelas** → `/daftar` (pencil-square), **Cabang** → `#daftar-kelas` (map-pin), **Cara Mendaftar** → `#cara-mendaftar` (clipboard-document-list). Anchor baru `id="daftar-kelas"` & `id="cara-mendaftar"` (+ `scroll-mt-24`).
- [x] **Navbar tamu** (`views/layouts/header.php`): untuk `empty($user)` container pill memakai `md:grid md:grid-cols-[1fr_auto_1fr]` — logo kiri (`md:justify-self-start`), **Beranda + Katalog Kelas tengah** (`md:justify-self-center`), Login + Registrasi kanan (`md:ml-0 md:justify-self-end`; `ml-auto` menjaga wrap di HP). Role guru/admin/peserta tidak berubah.
- [x] **Tagline hero**: paragraf panjang diganti `Belajar Quran, Mudah dan Menyenangkan` dengan gaya `text-lg font-medium sm:text-xl`.
- [x] Uji: `php -l` **111/111** bersih; smoke test curl beranda tamu (200, tagline & bar ikon tampil, 4 pintasan + 2 anchor benar), `/katalog` & `/daftar` (200), login admin + `/admin/dashboard` (200, `<main>` & nav admin tidak berubah); DOM seimbang (beranda 246/246, admin 55/55; `<nav>` 1/1, `<section>` 6/6), tanpa error PHP.

## Fase 12 — Hari kelas + gelombang "Coming Soon" + hero full-screen slider

Permintaan user: (1) pemilih **hari** (opsional, checkbox) pada kelas; (2) halaman **Coming Soon** berupa gelombang/angkatan per program yang bisa diminati user login dan dipindahkan admin ke kelas nyata; (3) hero beranda berupa **slider foto full-screen** berisi program yang sedang berjalan **dan** yang akan datang, dengan label program **di dalam gambar**, foto menimpa area navbar (tanpa celah).

- [x] **`kelas.hari`** (`database.sql` + `ALTER TABLE` di DB live): `VARCHAR(100) NULL` setelah `mode`, disimpan sebagai string koma (mis. `Senin,Rabu,Jumat`). Form admin kelas memakai checkbox `hari[]`; `admin/Kelas::dataInput()` memfilter ke daftar hari valid. Helper baru **`jadwal_tampil($kelas)`** (`helpers/format_helper.php`) merender `Hari | Jam (Zona)` dengan fallback ke kolom `jadwal` lama, dipakai di admin/guru/peserta/katalog/daftar (PHP + JS payload `_pilih.php`).
- [x] **Gelombang / Coming Soon**: tabel baru `gelombang` (program, angkatan, `tgl_buka`, `tgl_tutup`, status) + `pendaftaran_gelombang` (gelombang, user, peserta opsional, status) di `database.sql` dan DB live. Model `Gelombang_model` & `Pendaftaran_gelombang_model` (di-autoload). Admin `/admin/gelombang`: CRUD + daftar **calon** + proses pemindahan calon ke kelas nyata (membuat baris `pendaftaran`; penempatan level hanya sebagai opsi bila peserta belum punya level). Publik `/gelombang` (daftar gelombang buka) + `/gelombang/daftar/<id>` (wajib login); peserta punya halaman riwayat minat. Nav admin + nav publik ditambah tautannya.
- [x] **Seed `Level Gabungan`** (urutan 9) ditambahkan ke `database.sql` dan DB live.
- [x] **Hero full-screen slider** (`controllers/Beranda.php` + `views/beranda.php`): `gambarHeroSlider` dibangun dari program dengan kelas aktif (`status='berjalan'`) **dan** gelombang yang sedang buka (`akan_datang`), **satu foto per program** (program yang masuk dua-duanya diprioritaskan `berjalan`), minimal 2 slide, fallback `hero.jpg`. Helper baru `program_gambar_list()`.
- [x] **Tampilan slider**: `#hero-slider` `absolute inset-x-0 -top-40 bottom-0` (foto menarik ke atas menimpa area header), tiap slide fade 1 detik, rotasi 5 detik, dot indikator; overlay gradient gelap dari kiri. Label **di dalam gambar** (kiri bawah): badge `Kelas Berjalan` / `Segera Hadir`, `KODE — nama program`, plus `angkatan • periode` untuk coming soon. Tinggi hero `min-h-[85vh]` (HP) / `calc(100vh-72px)` (desktop). Kartu "Kelas yang Berjalan" & "Coming Soon" jadi glass card di kanan (desktop) dan kartu normal di bawah hero (mobile). Margin negatif `-mt-8/-mt-10` pada bar "Pintasan Cepat" dihapus agar tidak ada kotak menumpuk.
- [x] **Navbar tamu** (`views/layouts/header.php`): `sticky top-0 z-40` dipertahankan (tetap terlihat saat scroll) dan karena `z-40` > stacking context hero, pill navbar melayang **di atas** foto; latar header transparan sehingga foto terlihat di sekeliling pill.
- [x] **Kartu "Program yang Berjalan"** (bukan level): `views/beranda.php` menghitung `$programBerjalan` sekali dari `$programs` + `$rekap` (`rekapProgram()`), 1 baris per program berisi kode, nama program, jumlah kelas, dan cabang pemilik kelas. Level tidak lagi ditampilkan di kartu ini (level tetap muncul di daftar kelas/katalog). Berlaku untuk kartu glass desktop dan kartu mobile; badge mobile menampilkan jumlah kelas.
- [x] **Navbar gaya kaca (glassmorphism)**: pill nav (dipakai tamu & role non-admin) diubah dari `bg-white/80` menjadi `bg-white/40` + gradient `from-white/55 to-white/30`, `border-white/50`, `ring-1 ring-white/40`, `backdrop-blur-2xl backdrop-saturate-150`, dan bayangan lembut. Hover link jadi `hover:bg-white/70` (bukan chip krem solid) dan warna teks nav dinaikkan `text-slate-600 → text-slate-700` agar tetap kontras ≥ AA di atas foto. Logo JPG (latar putih penuh) dibungkus chip `bg-white/95` kecil supaya tidak tampak kotak putih.
- [x] Uji: `php -l` bersih untuk semua berkas tersentuh; smoke test curl beranda → 3 slide (`BIHAR`, `BADAL` = berjalan; `BILAL` = segera hadir) + label benar; pengukuran DOM via Chrome headless (1366×820 & 390×844): header `top 0 / bottom 66`, slider `top -94` → menutup area navbar **tanpa celah**, `<h1>` mulai di 196 (tidak tertutup navbar).

## Di luar scope (jangan dikerjakan)

Payment gateway, notifikasi WA/email, sertifikat, aplikasi mobile, automated testing (kecuali diminta user).
