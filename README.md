<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">🗺️ Web GIS Wilayah Kabupaten Jember & Sistem Pendukung Keputusan (SPK) SAW</h1>

<p align="center">
  <strong>Sistem Informasi Geografis Berbasis Web Pemetaan 31 Kecamatan di Kabupaten Jember yang Terintegrasi dengan Sistem Pendukung Keputusan (SPK) Multi-Skenario Metode Simple Additive Weighting (SAW)</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/Leaflet-1.9.4-199900?style=for-the-badge&logo=leaflet&logoColor=white" alt="Leaflet.js">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Data_Source-BPS_Jember_2024-005596?style=for-the-badge" alt="BPS 2024">
</p>

---

## 📌 Tentang Proyek

Proyek ini adalah aplikasi **Web GIS (Geographic Information System)** modern yang memetakan seluruh **31 wilayah administratif kecamatan di Kabupaten Jember, Jawa Timur**. Sistem ini dibangun dengan mengintegrasikan data spasial batas poligon wilayah (GeoJSON) bersama data demografi dan statistik resmi dari buku **"Kabupaten Jember Dalam Angka 2024"** yang diterbitkan oleh **Badan Pusat Statistik (BPS) Kabupaten Jember**.

Keunggulan utama sistem ini terletak pada modul **Sistem Pendukung Keputusan (SPK)** yang menggunakan algoritma **Simple Additive Weighting (SAW)** dengan **2 skenario keputusan yang berbeda**. Hasil analisis disajikan secara visual melalui **Peta Tematik Choropleth** interaktif, tabel matriks normalisasi, medali podium, dan perbandingan dinamis antar-skenario.

---

## ✨ Fitur-Fitur Utama

### 1. 🗺️ Peta Spasial Web GIS Interaktif (Leaflet.js)
* **31 Batas Wilayah GeoJSON**: Visualisasi poligon batas kecamatan lengkap dengan penyesuaian area pandang otomatis (*auto-fit bounds*).
* **Multi-Basemap Tile Layers**: Dukungan 3 jenis peta dasar yang dapat diganti sewaktu-waktu:
  * 🗺️ *OpenStreetMap Standard*
  * 🏢 *CartoDB Positron* (Clean Light, kontras optimal untuk peta tematik)
  * 🛰️ *Esri World Imagery* (Citra Satelit Resolusi Tinggi)
* **Pencarian Cepat (*Quick Jump District*)**: Dropdown pencarian yang langsung menerbangkan (*flyToBounds*) kamera peta ke poligon kecamatan yang dipilih dan membuka popup informasinya.
* **Popup Komprehensif Berbasis Data**: Menampilkan identitas kecamatan, perbandingan hasil ranking SPK 1 vs SPK 2 berdampingan, ringkasan statistik BPS, tombol *Zoom*, dan link langsung ke detail wilayah.
* **Kontrol Interaktif Lanjutan**:
  * Label centroid nama kecamatan yang menempel di atas peta (*toggleable*).
  * Mode Layar Penuh (*Fullscreen API*).
  * Kontrol skala metrik dan koordinat kursor mouse *real-time*.

### 2. 🎨 Peta Tematik Choropleth SPK (Multi-Skenario)
* **Pewarnaan Gradasi 5 Tingkat Kategori SAW**:
  * 🟥 **Sangat Tinggi** (`#e11d48`) — *Prioritas Tertinggi (Top 20%)*
  * 🟧 **Tinggi** (`#f97316`) — *Tingkat 2*
  * 🟨 **Sedang** (`#eab308`) — *Tingkat 3*
  * 🟦 **Rendah** (`#0284c7`) — *Tingkat 4*
  * ⬜ **Sangat Rendah** (`#64748b`) — *Tingkat 5*
* **Mode Switcher Instan**: Tombol beralih dalam 1 klik antara:
  1. *Batas Wilayah Netral*
  2. *Choropleth SPK 1 (Potensi Demografi)*
  3. *Choropleth SPK 2 (Beban Administrasi)*
* **Legenda Peta Dinamis (*Floating Legend*)**: Keterangan warna dan rentang ranking yang muncul otomatis sesuai skenario aktif.
* **Filter Kategori Spasial**: Pengguna dapat memfilter peta berdasarkan kategori tertentu (kecamatan di luar kategori akan diredupkan secara elegan).

### 3. 🧠 Sistem Pendukung Keputusan (SPK) Metode SAW
Sistem menyediakan **2 Skenario Pengambilan Keputusan**:

| Parameter | Skenario 1 (SPK 1) | Skenario 2 (SPK 2) |
| :--- | :--- | :--- |
| **Judul Analisis** | **Analisis Potensi & Dinamika Demografi** | **Prioritas Beban Pelayanan Administrasi Wilayah** |
| **Tujuan Keputusan** | Menentukan kecamatan dengan basis pasar, aktivitas ekonomi, dan potensi investasi terbesar | Menentukan kecamatan dengan prioritas alokasi aparatur sipil, kantor pelayanan, & beban kerja birokrasi |
| **Kriteria & Bobot** | • Jumlah Penduduk (**60%**, Benefit)<br>• Laju Pertumbuhan Penduduk (**40%**, Benefit) | • Jumlah Desa/Kelurahan (**45%**, Benefit)<br>• Jumlah Penduduk (**35%**, Benefit)<br>• Laju Pertumbuhan Penduduk (**20%**, Benefit) |
| **Juara 1 (Rank #1)** | 🥇 **Kecamatan Sumbersari** (Skor: **100.00%**) | 🥇 **Kecamatan Bangsalsari** (Skor: **87.09%**) |

* **Fitur Halaman SPK**:
  * Kartu Kriteria & Bobot dengan bar animasi interaktif.
  * Podium Medali Top 3 (🥇🥈🥉) untuk kecamatan terbaik.
  * Tabel Ranking Lengkap 31 kecamatan menampilkan nilai asli ($x_{ij}$), matriks normalisasi ($r_{ij}$), kontribusi bobot ($W_j$), dan skor akhir ($V_i$).
  * Panel **Perbandingan Dinamis SPK 1 vs SPK 2** (menyoroti pergeseran peringkat akibat perbedaan kriteria; contoh: *Kecamatan Puger naik dari #7 di SPK 1 ke #2 di SPK 2 karena memiliki 12 desa*).
  * Panel Accordion Rumus Matematis SAW.

### 4. 🗃️ Manajemen Data Wilayah & Statistik BPS (CRUD Penuh)
* **Manajemen Kecamatan**: Penambahan, pengeditan, penghapusan, dan profil detail 31 kecamatan beserta titik koordinat lintang & bujur.
* **Manajemen Statistik BPS**: Pembaruan jumlah penduduk, laju pertumbuhan, jumlah desa/kelurahan, dan tahun rilis BPS secara dinamis.
* **Pencarian Real-time (*Search*)**: Filter pencarian instan berdasarkan nama kecamatan.
* **Pengurutan Multi-Kolom (*Sort*)**: Pengurutan dinamis berdasarkan nama, jumlah penduduk, laju pertumbuhan, atau jumlah desa (Ascending / Descending).
* **Performa Teroptimasi**: Eager loading relasi Eloquent (`with('statistik')`) untuk mencegah masalah *N+1 query*.

---

## 📐 Landasan Teori: Metode Simple Additive Weighting (SAW)

Metode SAW (dikenal juga sebagai metode penjumlahan terbobot) mencari penjumlahan terbobot dari rating kinerja pada setiap alternatif di semua kriteria.

```
                   ┌──────────────────────────────────────┐
                   │  1. Matriks Keputusan (X)            │
                   └──────────────────┬───────────────────┘
                                      ▼
                   ┌──────────────────────────────────────┐
                   │  2. Normalisasi Matriks (R)          │
                   │     r_ij = x_ij / max(x_ij) [Benefit]│
                   │     r_ij = min(x_ij) / x_ij [Cost]   │
                   └──────────────────┬───────────────────┘
                                      ▼
                   ┌──────────────────────────────────────┐
                   │  3. Perkalian Bobot & Preferensi (V) │
                   │     V_i = Σ (W_j × r_ij)             │
                   └──────────────────┬───────────────────┘
                                      ▼
                   ┌──────────────────────────────────────┐
                   │  4. Perankingan & Klasifikasi        │
                   │     (Urutkan Vi Terbesar ke Terkecil)│
                   └──────────────────────────────────────┘
```

> **Catatan Teknis Penanganan Nilai Negatif**:
> Pada kriteria laju pertumbuhan penduduk, beberapa kecamatan mengalami penurunan penduduk sehingga bernilai minus (misal: `-0.65%`). Sistem ini secara cerdas menerapkan teknik *positive shifting* ($x_{ij}' = x_{ij} + |\min|$) sebelum normalisasi benefit dilakukan, sehingga tidak terjadi pembagian bilangan negatif yang merusak skala SAW.

---

## 🛠️ Arsitektur & Teknologi

* **Bahasa Pemrograman**: PHP 8.1+
* **Framework Web**: Laravel 10.x (MVC Architecture)
* **Pemetaan Spasial (Web GIS)**: Leaflet.js v1.9.4 & GeoJSON
* **Styling & Tampilan UI**: Tailwind CSS (CDN dengan Custom Design Tokens)
* **Tipografi & Ikon**: Plus Jakarta Sans (Google Fonts) & Font Awesome 6
* **Database**: MySQL / MariaDB (Didukung Seeder 31 Kecamatan BPS Jember)

---

## 📂 Struktur Direktori Utama

```
GIS-Jember/
├── app/
│   ├── Http/Controllers/
│   │   ├── KecamatanController.php   # Controller CRUD Wilayah, Search, & Sort
│   │   ├── StatistikController.php   # Controller CRUD Data Statistik BPS
│   │   ├── SpkController.php         # Controller Halaman SPK 1, SPK 2, & API JSON
│   │   └── PetaController.php        # Controller Peta Leaflet & Integrasi Choropleth
│   ├── Models/
│   │   ├── Kecamatan.php             # Model Wilayah (HasOne ke DataStatistik)
│   │   └── DataStatistik.php         # Model Statistik BPS (BelongsTo ke Kecamatan)
│   └── Services/
│       └── SawService.php            # Service Engine Algoritma SAW Mandiri
├── database/
│   ├── migrations/                   # Skema tabel kecamatans & data_statistiks
│   └── seeders/                      # Seeder otomatis 31 Kecamatan & Data BPS 2024
├── public/
│   └── geojson/
│       └── jember_kecamatan.geojson  # File Data Spasial Batas 31 Kecamatan Jember
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php             # Layout Utama & Navigasi Dropdown Interaktif
│   ├── kecamatan/                    # View CRUD (index, create, edit, show)
│   ├── spk/
│   │   ├── spk1.blade.php            # View Hasil SPK 1 (Potensi Demografi)
│   │   └── spk2.blade.php            # View Hasil SPK 2 (Beban Administrasi)
│   └── peta/
│       └── index.blade.php           # View Web GIS, Kontrol Lanjutan, & Choropleth
└── routes/
    └── web.php                       # Rute Web & API Endpoint
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah mudah berikut untuk menjalankan proyek di komputer lokal (menggunakan **Laragon**, **XAMPP**, atau PHP CLI):

### 1. Kloning Repositori
```bash
git clone https://github.com/Ranggueeesssss/GIS_wilayah_Jember.git
cd GIS_wilayah_Jember
```

### 2. Pasang Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi File Lingkungan (`.env`)
Salin file konfigurasi sampel dan sesuaikan pengaturan database:
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan pastikan konfigurasi basis data Anda sesuai:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gis-jember
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Eksekusi Migrasi & Seeder Database
Perintah berikut akan membuat seluruh skema tabel dan mengisi otomatis data 31 kecamatan lengkap beserta data BPS Kabupaten Jember 2024:
```bash
php artisan migrate --seed
```

### 5. Jalankan Server Lokal
```bash
php artisan serve
```

Buka peramban (*browser*) Anda dan akses:
👉 **`http://127.0.0.1:8000`**

---

## 🛣️ Daftar Rute & Endpoint Aplikasi

| Rute URL | Metode | Controller & Method | Deskripsi |
| :--- | :---: | :--- | :--- |
| `/` | `GET` | Redirect ke `/kecamatan` | Halaman Pengalihan Utama |
| `/peta` | `GET` | `PetaController@index` | **Peta Spasial Web GIS & Choropleth SPK** |
| `/kecamatan` | `GET` | `KecamatanController@index` | Daftar Wilayah, Pencarian, & Pengurutan |
| `/kecamatan/create` | `GET` | `KecamatanController@create` | Form Tambah Data Kecamatan Baru |
| `/kecamatan/{id}` | `GET` | `KecamatanController@show` | Detail Profil Kecamatan & Statistik |
| `/kecamatan/{id}/edit`| `GET` | `KecamatanController@edit` | Form Ubah Data Kecamatan |
| `/spk/spk1` | `GET` | `SpkController@spk1` | **Hasil Analisis SPK 1 (Potensi Demografi)** |
| `/spk/spk2` | `GET` | `SpkController@spk2` | **Hasil Analisis SPK 2 (Beban Administrasi)** |
| `/api/spk/{scenario}`| `GET` | `SpkController@api` | API Endpoint JSON Hasil Perhitungan SAW |

---

## 📊 Sumber Referensi Data

1. **Badan Pusat Statistik (BPS) Kabupaten Jember**: Publikasi *"Kabupaten Jember Dalam Angka (Jember Regency in Figures) 2024"*.
2. **Data Spasial Wilayah**: Peta Batas Administrasi Kecamatan Kabupaten Jember (Format GeoJSON EPSG:4326 WGS84).

---

<p align="center">
  Dibuat dengan dedikasi untuk pengembangan Web GIS dan Sistem Pendukung Keputusan Wilayah Kabupaten Jember.
</p>
