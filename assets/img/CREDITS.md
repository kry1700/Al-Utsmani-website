# Kredit Gambar

Semua gambar berasal dari **Wikimedia Commons** (lisensi bebas; CC0 = tanpa syarat,
CC BY-SA = wajib mencantumkan kredit + membagikan ulang dengan lisensi sama).
Gambar diunduh lokal agar situs tidak bergantung pada server luar.

| Berkas | Dipakai untuk | Judul asli | Penulis | Lisensi |
|---|---|---|---|---|
| `hero.jpg` | Hero beranda | [Tadarus di mesjid](https://commons.wikimedia.org/wiki/File:Tadarus_di_mesjid.jpg) | Dikyedarling | CC BY-SA 4.0 |
| `program-bihar.jpg` | Carousel program BIHAR | [Muslim school children Peradeniya](https://commons.wikimedia.org/wiki/File:Muslim_school_children_Peradeniya.jpg) | Bergentroll | CC0 |
| `program-badal.jpg` | Carousel program BADAL | [HTN0013](https://commons.wikimedia.org/wiki/File:HTN0013.jpg) | AstridDreams | CC0 |
| `program-bilal.jpg` | Carousel program BILAL | [Tadarus Ramadhan](https://commons.wikimedia.org/wiki/File:Tadarus_Ramadhan.jpg) | Albert Ivan Damanik | CC BY-SA 4.0 |
| `program-pdhq.jpg` | Carousel program PDHQ | [Taklim di Pesantren 02](https://commons.wikimedia.org/wiki/File:Taklim_di_Pesantren_02.jpg) | Muhamad Izzul Fiqih | CC BY-SA 4.0 |
| `cabang-jakarta-timur.jpg` | Kartu cabang wilayah Jakarta Timur | [Tower Kantor Walikota Jakarta Timur](https://commons.wikimedia.org/wiki/File:Tower_Kantor_Walikota_Jakarta_Timur_-_panoramio.jpg) | sugeng widodo | CC BY-SA 3.0 |
| `cabang-bekasi.jpg` | Kartu cabang wilayah Bekasi | [Bekasi Station 05](https://commons.wikimedia.org/wiki/File:Bekasi_Station_05.jpg) | Afrogindahood | CC BY-SA 3.0 |
| `cabang-cirebon.jpg` | Kartu cabang wilayah Cirebon | [Kantor Bupati Cirebon](https://commons.wikimedia.org/wiki/File:Kantor_Bupati_Cirebon.jpg) | Jie73 | CC BY-SA 3.0 |
| `cabang-padang.jpg` | Kartu cabang wilayah Padang | [Padang City from Gunung Padang](https://commons.wikimedia.org/wiki/File:Padang_City_as_seen_from_the_peak_of_Gunung_Padang,_2017-02-14.jpg) | Crisco 1492 | CC BY-SA 4.0 |
| `cabang-bogor.jpg` | Kartu cabang wilayah Bogor | ["Kolam Astrid" in Bogor Botanical Garden](https://commons.wikimedia.org/wiki/File:%22Kolam_Astrid%22_in_Bogor_Botanical_Garden.jpg) | Apri DAV | CC BY-SA 4.0 |
| `cabang-sorong.jpg` | Kartu cabang wilayah Sorong | [Sorong view-1](https://commons.wikimedia.org/wiki/File:Sorong_view-1.jpg) | Lord Mountbatten | CC BY-SA 4.0 |

Catatan:
- Resolusi 960–1280 px, ukuran berkas ≤ ~280 KB (tanpa GD/ImageMagick di server XAMPP ini).
- Bila gambar diganti, cukup timpa berkas dengan nama sama — helper `program_gambar()`
  di `application/helpers/format_helper.php` memetakannya per kode program
  (`BIHAR` → `program-bihar.jpg`, dst.) dan otomatis memakai fallback gradient
  bila berkasnya tidak ada.
- Gambar cabang dipetakan per **wilayah** lewat helper `cabang_gambar()`
  (`Jakarta Timur` → `cabang-jakarta-timur.jpg`, dst.); cabang dalam wilayah yang
  sama memakai foto wilayah tersebut. Fallback gradient + singkatan nama cabang
  dipakai bila berkasnya tidak ada.
