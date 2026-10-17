# IDSS UMKM — Fuzzy Mamdani Decision Support System
## Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM

Aplikasi web *Intelligent Decision Support System* (IDSS) berbasis MVC lengkap yang dirancang secara profesional untuk membantu evaluasi dan analisis kelayakan usaha Mikro, Kecil, dan Menengah (UMKM) menggunakan metode penalaran **Fuzzy Mamdani (MIN-MAX-Centroid)** berdasarkan dataset empiris penelitian.

---

## 1. Identitas & Tujuan Sistem

- **Nama Aplikasi:** IDSS UMKM — Fuzzy Mamdani Decision Support System
- **Subjudul:** Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM
- **Framework Backend:** PHP dengan CodeIgniter 3 (MVC Architecture)
- **Database:** MySQL / MariaDB (InnoDB, Foreign Keys, Prepared Statements)
- **Frontend & UI/UX:** HTML5, CSS3, JavaScript, Bootstrap 4, AdminLTE 3 (Modern Blue-White Palette)
- **Visualisasi & Grafik:** Chart.js
- **Tabel Interaktif:** DataTables Bootstrap 4 & Native Responsive Container
- **Autentikasi & Keamanan:** CodeIgniter 3 Session, Bcrypt Password Hashing, CSRF Protection, XSS Filtering, Role-Based Access Control (`admin` & `operator`), Audit Activity Trail

### Tujuan Aplikasi:
1. Mengelola dataset UMKM sumber penelitian secara akurat tanpa mengubah atau mendistorsi data asli.
2. Mengolah data menggunakan inferensi Fuzzy Mamdani 3 variabel masukan dengan 27 aturan terstruktur.
3. Menghasilkan skor indeks kelayakan skala 0–100 melalui metode defuzzifikasi **Centroid**.
4. Mengelompokkan hasil ke dalam kategori: **Tidak Layak** (<40), **Cukup Layak** (40–69), dan **Layak** (70–100).
5. Menyediakan transparansi penalaran (fuzzifikasi, aktivasi aturan, kurva agregasi, dan interpretasi tertulis).
6. Menyediakan rekapitulasi, komparasi antarevaluasi, riwayat, cetak lembar hasil, dan ekspor data (CSV).
7. Menjadi sistem pendukung keputusan (decision support system), bukan pengganti mutlak keputusan perbankan formal.

---

## 2. Spesifikasi Metodologi Fuzzy Mamdani v1.0

### A. Variabel Masukan (Inputs)
1. **Modal / Investasi (Rp):**
   - Domain prototipe: Rp500.000 s.d. Rp900.000.000
   - *Rendah (Trapesium Bahu Kiri):* `(500.000, 500.000, 5.000.000, 14.000.000)`
   - *Sedang (Segitiga):* `(5.000.000, 14.000.000, 50.000.000)`
   - *Tinggi (Trapesium Bahu Kanan):* `(14.000.000, 50.000.000, 900.000.000, 900.000.000)`

2. **Lama Usaha (Tahun):**
   - Domain prototipe: 1 s.d. 15 Tahun
   - *Pendek (Trapesium Bahu Kiri):* `(1, 1, 4, 8)`
   - *Menengah (Segitiga):* `(4, 8, 11)`
   - *Panjang (Trapesium Bahu Kanan):* `(8, 11, 15, 15)`

3. **Stabilitas Pendapatan:**
   - Kategori masukan tegas (*crisp singleton*):
     - `Fluktuatif` (Level 0, $\mu = 1.0$)
     - `Cukup stabil` (Level 1, $\mu = 1.0$)
     - `Stabil` (Level 2, $\mu = 1.0$)

### B. Variabel Keluaran (Output)
- Domain skor kelayakan: 0 s.d. 100
- *Rendah (Trapesium Bahu Kiri):* `(0, 0, 30, 50)`
- *Sedang (Segitiga):* `(30, 50, 70)`
- *Tinggi (Trapesium Bahu Kanan):* `(50, 70, 100, 100)`

### C. Basis Aturan Inferensi (27 Aturan Eksplisit)
Pembentukan aturan menggunakan penjumlahan level kombinasi ($0+0+0 = 0$ hingga $2+2+2 = 6$):
- Total level 0–1 $\rightarrow$ **Rendah** (4 aturan: R01, R02, R04, R10)
- Total level 2–4 $\rightarrow$ **Sedang** (19 aturan: R03, R05-R09, R11-R17, R19-R23, R25)
- Total level 5–6 $\rightarrow$ **Tinggi** (4 aturan: R18, R24, R26, R27)

### D. Operator & Defuzzifikasi
- **Operator AND:** $\text{MIN}(\mu_{\text{modal}}, \mu_{\text{lama}}, \mu_{\text{stabilitas}})$
- **Implikasi:** $\text{MIN}(\alpha_i, \mu_{\text{output}}(z))$
- **Agregasi Antaraturan:** $\text{MAX}$ untuk seluruh konsekuen
- **Defuzzifikasi:** Centroid / Center of Gravity (COG):
  $$z^* = \frac{\int z \cdot \mu_{\text{agg}}(z) \, dz}{\int \mu_{\text{agg}}(z) \, dz}$$
  Diimplementasikan secara diskret dengan resolusi numerik partisi $\Delta z = 0.1$ (1001 titik sampel).

### E. Batas Kategori Keputusan Akhir
- **Skor < 40.0:** *Tidak Layak* (Badge Merah Lembut)
- **Skor 40.0 s.d. < 70.0:** *Cukup Layak* (Badge Kuning/Amber Lembut)
- **Skor 70.0 s.d. 100.0:** *Layak* (Badge Hijau Lembut)

---

## 3. Struktur Direktori Proyek

```
idss_umkm_fuzzy/
├── application/
│   ├── config/
│   │   ├── autoload.php          # Autoload libraries, helpers, and models
│   │   ├── config.php            # Base URL dinamis, CSRF, security key
│   │   ├── database.php          # Konfigurasi koneksi MySQL idss_umkm
│   │   └── routes.php            # Routing default dashboard & auth
│   ├── controllers/
│   │   ├── Auth.php              # Login, logout, dan manajemen profil
│   │   ├── Dashboard.php         # Kartu statistik dinamis & grafik Chart.js
│   │   ├── Umkm.php              # CRUD UMKM, filter, import Excel/CSV, export
│   │   ├── Evaluasi.php          # Form evaluasi, simulasi, inferensi Mamdani
│   │   ├── Hasil_evaluasi.php    # Riwayat evaluasi, rincian, bandingkan, cetak
│   │   ├── Laporan.php           # Rekap kategori, bidang usaha, kecamatan
│   │   ├── Konfigurasi_fuzzy.php # Tampilan parameter aktif & 27 aturan
│   │   ├── Pengguna.php          # Manajemen user & role (Admin only)
│   │   └── Log_aktivitas.php     # Audit trail log sistem (Admin only)
│   ├── core/
│   │   └── MY_Controller.php     # Base controller (auth check, role guard, layout)
│   ├── libraries/
│   │   ├── Fuzzy_mamdani.php     # Mesin inferensi Fuzzy Mamdani independen
│   │   └── Import_service.php    # Parser cerdas CSV & XLSX native
│   ├── models/
│   │   ├── User_model.php
│   │   ├── Umkm_model.php
│   │   ├── Fuzzy_model.php
│   │   ├── Evaluation_model.php
│   │   ├── Report_model.php
│   │   └── Activity_log_model.php
│   ├── tests/
│   │   ├── test_fuzzy_engine.php    # Unit test mesin fuzzy (66 assertions)
│   │   ├── test_import.php          # Uji parser dataset 560 UMKM Soppeng
│   │   ├── test_integration.php     # Uji integrasi pipeline database & evaluasi
│   │   ├── test_http_endpoints.php  # Uji HTTP GET/POST endpoints
│   │   └── test_operator_role.php   # Uji pembatasan otorisasi role operator
│   └── views/
│       ├── layouts/main.php      # Master layout AdminLTE 3 tema biru-putih
│       ├── auth/                 # Login & profil
│       ├── dashboard/            # Tampilan kartu statistik & grafik
│       ├── umkm/                 # Index tabel, detail, form, import
│       ├── evaluasi/             # Form evaluasi & hasil transparan
│       ├── hasil_evaluasi/       # Arsip, rincian, bandingkan, cetak
│       ├── laporan/              # Rekapitulasi & print preview
│       ├── konfigurasi_fuzzy/    # Parameter model & tabel 27 aturan
│       ├── pengguna/             # Kelola akun administrator & operator
│       └── log_aktivitas/        # Tabel jejak audit
├── assets/
│   ├── css/
│   │   └── custom.css            # Palet biru-putih, radius card, soft badges
│   └── js/
├── database/
│   ├── idss_umkm.sql             # Skema DDL lengkap + seed awal
│   └── seed_fuzzy_v1.sql         # Seed parameter model fuzzy v1.0
├── index.php                     # Front controller CodeIgniter 3
└── README.md                     # Dokumentasi komprehensif sistem
```

---

## 4. Akun Pengguna Awal (Default Credentials)

Sistem telah dilengkapi dengan 2 akun pengguna terverifikasi:

| Role | Username | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `Admin#2026!` | Akses penuh: Pengguna, Log Audit, Konfigurasi Model, CRUD UMKM, Evaluasi, Laporan |
| **Operator** | `operator` | `Operator#2026!` | Pengelolaan data UMKM, Pelaksanaan Evaluasi Fuzzy, Melihat Hasil & Laporan (Dilarang akses kelola user & log) |

*Password dienkripsi menggunakan standar `PASSWORD_BCRYPT`.*

---

## 5. Panduan Instalasi & Menjalankan Aplikasi

### Persyaratan Lingkungan:
- PHP versi 7.4 s.d. 8.2 (Direkomendasikan PHP 8.2 pada XAMPP)
- Ekstensi PHP aktif: `mysqli`, `session`, `zip`, `mbstring`, `fileinfo`
- MySQL 5.7+ atau MariaDB 10.3+

### Langkah 1: Persiapan Database via phpMyAdmin
1. Buka browser dan buka `http://localhost/phpmyadmin/`.
2. Klik tab **Import** (atau buat database baru bernama `idss_umkm`).
3. Pilih file `database/idss_umkm.sql`.
4. Klik tombol **Import / Go**. Seluruh tabel (`users`, `umkm`, `fuzzy_models`, `evaluations`, `activity_logs`) dan akun awal akan otomatis terbentuk.

### Langkah 2: Menjalankan Aplikasi Web
Anda dapat menjalankan aplikasi melalui dua cara yang fleksibel:

#### Opsi A: Melalui Server Pengembangan PHP CLI (Rekomendasi Cepat)
Jalankan perintah berikut di PowerShell atau Command Prompt pada folder proyek:
```bash
cd C:\Users\itsme\.gemini\antigravity\scratch\idss_umkm_fuzzy
"C:\xampp\php\php.exe" -S 127.0.0.1:8080
```
Buka browser dan akses:
**`http://127.0.0.1:8080/`** atau **`http://127.0.0.1:8080/index.php/login`**

#### Opsi B: Melalui Web Server Apache XAMPP
Folder proyek telah terhubung langsung ke direktori `htdocs` XAMPP melalui junction folder:
`C:\xampp\htdocs\idss_umkm_fuzzy`
1. Buka XAMPP Control Panel dan klik tombol **Start** pada modul Apache dan MySQL.
2. Buka browser dan akses:
**`http://localhost/idss_umkm_fuzzy/`**

---

## 6. Fitur Unggulan Sistem

1. **Parser Impor Cerdas (CSV & XLSX):**
   - Mendeteksi header kolom eksplisit dari dataset asli Soppeng (560 UMKM).
   - Mengabaikan baris judul non-tabel dan sel formatting kosong.
   - Otomatis menyaring baris ringkasan footer seperti baris `JUMLAH` atau `TOTAL`.
   - Membersihkan format teks mata uang `Rp` dan pemisah ribuan.
   - Pilihan penanganan ID duplikat: **Lewati (Skip)** atau **Perbarui (Update)** dengan proteksi transaksi database.

2. **Mesin Fuzzy Mamdani Transparan (Explainable AI / XAI):**
   - Menghitung derajat keanggotaan setiap masukan ($\mu$).
   - Menampilkan evaluasi seluruh 27 aturan dan menandai aturan aktif secara visual.
   - Menggambarkan kurva poligon agregasi MAX-MIN menggunakan **Chart.js**.
   - Menyajikan nilai momen numerik, luas area, dan skor defuzzifikasi Centroid.
   - Narasi interpretasi otomatis dan rekomendasi pembinaan/fasilitasi pembiayaan.

3. **Modul Komparasi Antarevaluasi:**
   - Membandingkan dua hasil evaluasi secara berdampingan (*side-by-side*).
   - Menganalisis selisih pergeseran modal, lama usaha, kekuatan potong $\alpha$, dan pergeseran skor kelayakan.

4. **Dashboard Aktual & Ekspor Lengkap:**
   - Visualisasi distribusi kategori, bidang usaha, stabilitas, dan tren evaluasi per bulan.
   - Ekspor dataset UMKM dan riwayat evaluasi ke CSV dengan penambahan UTF-8 BOM untuk kompatibilitas penuh dengan Microsoft Excel.
   - Format cetak ramah cetak browser (*print-friendly view*).

---

## 7. Hasil Pengujian Otomatis (Test Suites)

Aplikasi telah dilengkapi unit test dan integration test yang dapat diverifikasi kapan saja:

### 1. Uji Mesin Fuzzy Mamdani (66 Assertions - 100% PASS)
```bash
"C:\xampp\php\php.exe" application/tests/test_fuzzy_engine.php
```
*Menguji fungsi segitiga, trapesium, batas domain, kelengkapan 27 aturan, operator MIN-MAX, centroid numerik analitis, penanganan input invalid, dan konsistensi hasil.*

### 2. Uji Integrasi Dataset Empiris (560 Records - 100% PASS)
```bash
"C:\xampp\php\php.exe" application/tests/test_integration.php
```
*Memverifikasi pembacaan file Excel Soppeng 560 baris, penyimpanan ke MySQL tabel `umkm`, evaluasi fuzzy riil, dan agregasi statistik dashboard.*

### 3. Uji End-to-End HTTP Endpoints (100% PASS)
```bash
"C:\xampp\php\php.exe" application/tests/test_http_endpoints.php
```
*Memverifikasi alur otentikasi login, token CSRF, proteksi sesi, dan respons HTTP 200 OK pada seluruh halaman aplikasi.*

### 4. Uji Otorisasi Role Operator (100% PASS)
```bash
"C:\xampp\php\php.exe" application/tests/test_operator_role.php
```
*Memverifikasi bahwa akun Operator berhasil diblokir dari menu administratif `/pengguna` dan `/log_aktivitas`.*

---

## 8. Catatan Metodologis & Batasan Sistem

- Variabel keuangan tambahan seperti Omzet, Laba, Biaya Operasional, dan Arus Kas Bersih disimpan sebagai data sumber penelitian dan tidak otomatis dijadikan variabel masukan fuzzy v1.0 untuk menghindari multikolinearitas.
- Konfigurasi parameter fungsi keanggotaan dan 27 aturan adalah rancangan model prototipe v1.0. Penyesuaian batas kategori atau rentang domain untuk penelitian masa depan dapat didokumentasikan dengan membuat snapshot versi model baru di tabel `fuzzy_models` tanpa mengubah arsip evaluasi masa lalu.
- Hasil sistem pendukung keputusan ini dimaksudkan untuk rekomendasi penunjang kelayakan dan evaluasi internal pembinaan UMKM, bukan instrumen putusan hukum pembiayaan perbankan.

---
**Pengembang:** Tim Senior AI & Software Engineering — Google DeepMind Antigravity  
**Tahun:** 2026
