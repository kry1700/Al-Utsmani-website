# Design System Guidelines (Donezo Theme — Rebrand LBQ Al-Utsmani)

> **Catatan rebrand**: Palet brand kini **coklat Caramel/Gold-brown** (menggantikan
> Forest Green). Warna di bawah sudah diselaraskan dengan token di
> `application/views/layouts/header.php`. Skala `emerald`/`teal` Tailwind dipetakan
> ulang ke palet coklat, sehingga kelas lama otomatis mengikuti brand.

Dokumen ini berisi panduan sistem desain berdasarkan analisis visual aplikasi dashboard "Donezo". Panduan ini dirancang menggunakan pendekatan **Mobile-First** dan mencakup ekstensi gaya untuk halaman non-admin/publik agar konsisten secara brand.

---

## 1. Design Direction

* **Visual Style**: Modern, Clean, Soft-Skeuomorphic Touch (pada beberapa kartu aksen & dark card), Minimalist SaaS Interface.
* **Core Personality**: Professional, Efficient, Calming, Tech-forward.
* **Aesthetic Characteristics**:
  * Sudut membulat yang lembut dan konsisten (*high border radius*).
  * Kontras warna yang seimbang antara coklat karamel/gold-brown, krem hangat, abu-abu lembut, dan latar belakang putih/krem pucat.
  * *Pill-shaped elements* pada tombol, pencarian, badge status, dan item navigasi aktif.
  * Penggunaan elemen visual dengan *pattern/texture* bertema organik (misal pada widget *Time Tracker* dan *Download App*).

---

## 2. Color Palette

### Primary Colors (Brand Identity)
* **Primary Main**: `#8B5E34` (Caramel / Gold-Brown - Digunakan untuk tombol utama, kartu prioritas, dan status aktif)
* **Primary Light**: `#C89B6A` (Gold-Tan - Digunakan untuk progress bar, grafik, dan aksen positif)
* **Primary Soft**: `#F7EFE3` (Cream Background - Untuk state hover, latar item aktif, dan badge soft)

### Neutral Colors
* **Background Canvas**: `#FAF6F0` (Warm Off-White - Latar belakang utama aplikasi)
* **Surface / Container**: `#FFFFFF` (Pure White - Kartu, sidebar, header)
* **Text Primary**: `#1F1A16` (Dark Charcoal - Headings & teks utama)
* **Text Secondary**: `#7A6A5A` (Muted Gray - Sub-heading, label pendukung, dan placeholder)
* **Border / Divider**: `#EDE3D7` (Soft Gray Border)

### Status & Semantic Colors
* **Success / Completed**: `#8B5E34` (Text) | `#F7EFE3` (Bg Badge)
* **In Progress / Warning**: `#D97706` (Text) | `#FEF3C7` (Bg Badge)
* **Pending / Alert**: `#E11D48` (Text) | `#FFE4E6` (Bg Badge)

*(Asumsi: Warna semantic disesuaikan agar kontras accessible dengan WCAG AA).*

---

## 3. Typography

* **Font Family Utama**: `Plus Jakarta Sans`, `Inter`, atau `SF Pro Display` (Sans-Serif modern dengan *geometric/neo-grotesque touch*).

### Scale & Hierarchy
* **Display / Hero** *(Public Pages)*: `36px` - `48px` | Bold (`700`) | Line Height `1.2`
* **Heading 1 (H1 - Page Title)**: `24px` - `28px` | SemiBold/Bold (`600`/`700`) | Line Height `1.3`
* **Heading 2 (H2 - Card Title)**: `18px` - `20px` | SemiBold (`600`) | Line Height `1.4`
* **Heading 3 (H3 - Subsection)**: `15px` - `16px` | SemiBold (`600`) | Line Height `1.4`
* **Body Regular**: `14px` | Regular (`400`) | Line Height `1.5`
* **Body Small / Subtext**: `12px` | Regular (`400`) | Line Height `1.4`
* **Caption / Micro Text**: `10px` - `11px` | Medium (`500`) | Line Height `1.2`

---

## 4. Spacing System

Menggunakan basis **4pt / 8pt grid system**:

* **xs**: `4px`
* **sm**: `8px`
* **md**: `16px`
* **lg**: `24px`
* **xl**: `32px`
* **2xl**: `48px`

---

## 5. Border Radius

* **Full / Pill**: `9999px` (Untuk Badge, Input Search, Nav Active State, Pill Button)
* **Large (Card & Container)**: `16px` - `20px` (Untuk Kartu utama, Widget, Modal)
* **Medium (Control Elements)**: `10px` - `12px` (Untuk Tombol standar, Input Field, Dropdown)
* **Small (Micro Elements)**: `6px` - `8px` (Untuk Avatar small, Tooltip, Tag)

---

## 6. Elevation & Shadows

* **Flat Neutral** (Default Kartu Dashboard): `none` dengan border `1px solid #EDE3D7` atau tanpa border pada background `#FAF6F0`.
* **Low Elevation (Card Hover & Dropdown)**: `0px 4px 12px rgba(0, 0, 0, 0.04)`
* **Medium Elevation (Modal & Floating Navigation)**: `0px 12px 32px rgba(0, 0, 0, 0.08)`
* **Glow Elevation (Primary Focus / Dark Card)**: `0px 8px 20px rgba(139, 94, 52, 0.25)`

---

## 7. Component Specifications

### Buttons
* **Primary Button**:
  * Background: `#8B5E34`
  * Text: `#FFFFFF`
  * Radius: Full Pill (`9999px`) atau `12px`
  * Padding: Vertical `10px`, Horizontal `20px`
  * Hover: `#6B4423`
* **Secondary / Outlined Button**:
  * Background: `Transparent` / `#FFFFFF`
  * Border: `1px solid #8B5E34`
  * Text: `#8B5E34`
  * Radius: Full Pill (`9999px`)
* **Icon-Only / Action Button**:
  * Shape: Circle (`36px` x `36px` atau `40px` x `40px`)
  * Border: `1px solid #EDE3D7`

### Form Controls
* **Search Input**:
  * Background: `#FFFFFF` atau `#F9FAFB`
  * Border: `1px solid #EDE3D7`
  * Radius: Full Pill (`9999px`)
  * Prefix Icon: Magnifying Glass
  * Suffix: Shortcut Key Badge (misal `⌘F`)
* **Standard Inputs**:
  * Height: `42px` - `48px`
  * Radius: `10px`
  * Focus Ring: `2px solid #8B5E34` dengan opacity soft.

### Cards
* **Metric Stat Card**:
  * Default: Background White (`#FFFFFF`), Border Radius `16px`, Padding `20px`.
  * Highlight / Active Card: Background `#8B5E34`, Teks Putih, Icon Lingkaran dengan panah diagonal top-right (`↗`).
* **Feature Dark Card (Time Tracker / CTA)**:
  * Background: Gradient Dark Caramel (`#3F2915` ke `#8B5E34`) dengan pola garis/wave transparan.
  * Radius: `16px` - `20px`.

### Navigation
* **Sidebar (Desktop)**:
  * Container melayang (*floating/inset layout*) dengan background `#FFFFFF` dan border radius `20px`.
  * Item Navigasi Aktif: Background `#8B5E34`, Teks White, Left Accent Icon/Pill.
  * Badge Notifikasi: Pill kecil warna gelap atau kontras (`12+`).
* **Header Bar**:
  * Search Bar di kiri/tengah.
  * Profile & Quick Actions (Notification, Email Icon) di kanan.

### Tables & Data Lists (Team Collaboration / Project List)
* **ListItem Style** (bukan sekadar tabel tradisional kaku):
  * Padding baris: `12px 16px`
  * Divider: Border bottom halus `#F3F4F6`
  * Layout: Avatar + User Info (Left), Task Title (Center), Status Pill (Right).

### Modal & Dialogs *(Asumsi)*
* **Overlay**: `rgba(0, 0, 0, 0.4)` dengan effect `backdrop-blur(4px)`.
* **Container**: Radius `20px`, White background, Max-width `520px` (Desktop).
* **Header**: Title + Close Icon (`✕`).

---

## 8. Non-Admin / Public Pages Adaptation (Style Guide Extension)

Untuk halaman beranda, landing page, atau portal pengguna publik yang menggunakan tema desain ini:

1. **Hero Section**:
   * Gunakan judul besar (`36px` - `48px`) dengan warna `#1F1A16`.
   * Berikan highlight kata kunci menggunakan badge krem hangat (`#F7EFE3`) atau teks coklat `#8B5E34`.
2. **Cards & Feature Showcase**:
   * Terapkan gaya kartu melayang (Floating Cards dengan radius `20px` dan shadow halus).
   * Sertakan elemen abstrak berbentuk wave/lines berpola (seperti pada widget Time Tracker) sebagai latar ilustrasi fitur.
3. **Public Navigation Bar**:
   * Layout: *Floating Pill Bar* melayang di bagian atas halaman.
   * Background: Semi-transparent White `#FFFFFF` + `backdrop-blur(12px)` + Border `1px solid #EDE3D7`.
   * Radius: `9999px`.
   * Action Button: Primary Pill Button (`#8B5E34`).

---

## 9. Layout & Responsive Rules (Mobile-First)

### Screen Breakpoints
* **Mobile (SM)**: `320px` – `767px`
* **Tablet (MD)**: `768px` – `1023px`
* **Desktop (LG)**: `1024px` – `1279px`
* **Wide Desktop (XL)**: `1280px+`

---

### Component Adaptation Strategy

| Component | Mobile (`< 768px`) | Tablet (`768px - 1023px`) | Desktop (`1280px+`) |
| :--- | :--- | :--- | :--- |
| **Main Layout** | Single column stack, margin `12px` | 2-Column layout, margin `20px` | 3-Column Grid / Asymmetric Dashboard Layout |
| **Sidebar Navigation** | Tersembunyi (Drawer / Bottom Nav Pill Bar) | Collapsed Sidebar (Icon-only) | Full Floating Sidebar Panel (Left) |
| **Search & Header** | Search icon menyusut ke button modal, Profile ringkas | Search bar penuh di header | Search bar + Quick shortcut key (`⌘F`) |
| **Stat Cards** | Carousel horizontal (swipe) atau 2x2 Grid | Grid 2 Column | Grid 4 Column sejajar |
| **Chart / Analytics** | Kartu *Project Analytics* mengambil lebar 100%, grafik disederhanakan | 1 Column lebar penuh di atas | Grid 2-Column bersanding dengan Widget Reminders |
| **Tables / Data List** | Stack vertical per item, sembunyikan kolom tersier | Tabel standar dengan scroll horizontal jika perlu | Tabel penuh dengan detail avatar, status badge, & tindakan |
| **Dark Widget (Time Tracker/CTA)**| Posisi paling bawah halaman, full width | Grid 2-column di baris bawah | Sticky Sidebar Right / Bottom Widget Panel |
| **Modal** | Bottom Sheet (slide up dari bawah screen) | Centered Modal (`width: 80%`) | Centered Modal (`max-width: 520px`) |

---

## 10. Summary of Architectural Decisions (Assumptions Made)

1. **Font**: Karena logo dan UI menggunakan sans-serif modern dengan legibilitas tinggi, diputuskan menggunakan font variabel modern (`Plus Jakarta Sans`).
2. **Bottom Sheet Mobile**: Pada layar seluler, modal diubah dari dialog mengambang menjadi *Bottom Sheet* untuk meningkatkan thumb-reachability UX.
3. **Sidebar Behavioral Adjustment**: Di mobile, sidebar penuh diubah menjadi *Bottom Navigation Bar* atau *Slide-over Drawer*, menghindari penggunaan layar berlebih.
```

I telah membuatkan dokumen sistem desain `design-system.md` berdasarkan analisis visual screenshot aplikasi Donezo tersebut. 

Apakah ada komponen tambahan atau penyesuaian khusus pada breakpoint responsif yang ingin Anda ubah?