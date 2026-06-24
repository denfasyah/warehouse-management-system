# Database Schema

Tabel-tabel utama yang akan dibuat:

1. **users**
   - `id` (PK)
   - `name` (string)
   - `email` (string, unique)
   - `password` (string)
   - `role` (enum: admin, petugas)
   - timestamps

2. **categories**
   - `id` (PK)
   - `nama_kategori` (string)
   - timestamps

3. **locations**
   - `id` (PK)
   - `kode_rak` (string, unique)
   - `kelas_storage` (enum: fast, medium, slow)
   - `kapasitas` (integer)
   - timestamps

4. **items**
   - `id` (PK)
   - `kode_barang` (string, unique)
   - `nama_barang` (string)
   - `kategori_id` (FK to categories.id)
   - `stok` (integer)
   - `barcode` (string, unique)
   - `storage_class` (enum: fast, medium, slow)
   - `lokasi_id` (FK to locations.id)
   - timestamps

5. **incoming_goods**
   - `id` (PK)
   - `item_id` (FK to items.id)
   - `jumlah` (integer)
   - `tanggal` (date)
   - timestamps

6. **outgoing_goods**
   - `id` (PK)
   - `item_id` (FK to items.id)
   - `jumlah` (integer)
   - `status_approval` (enum: pending, approved, rejected)
   - `approved_by` (FK to users.id, nullable)
   - `tanggal` (date)
   - timestamps

7. **notifications**
   - `id` (PK)
   - `user_id` (FK to users.id)
   - `judul` (string)
   - `pesan` (text)
   - `status_baca` (boolean, default 0)
   - timestamps

# Dashboard Specification

## Admin Dashboard
- **Cards**: Total Barang, Total Kategori, Total User, Barang Fast Moving, Medium Moving, Slow Moving, Barang Masuk Hari Ini, Barang Keluar Hari Ini, Permintaan Approval (Pending).
- **Grafik**: Chart JS/ApexCharts membandingkan tren Barang Masuk vs Barang Keluar per bulan.
- **Aktivitas Terbaru**: List transaksi log terakhir (Approval, Input barang).

## Petugas Dashboard
- **Cards**: Barang Masuk Hari Ini, Barang Keluar Hari Ini, Total Scan Hari Ini.
- **Quick Action**: Tombol berukuran besar untuk: Scan Barcode, Form Barang Masuk, Permintaan Barang Keluar.

# UI/UX Requirement
- **Desain Modern**: Menggunakan kombinasi warna putih/abu terang dengan aksen biru/hijau korporat, *glassmorphism* di elemen card, bayangan (*box-shadow*) halus, serta *border-radius* melengkung.
- **Responsif**: Dashboard admin berfokus pada tampilan Desktop/Tablet. Dashboard Petugas **wajib** mobile-first dan *touch-friendly* dengan tombol besar.
- **Micro-interactions**: Efek *hover* pada menu, *spinner* saat memuat kamera scanner, transisi halus saat notifikasi muncul.
- **Tailwind CSS & Alpine.js**: Digunakan untuk membangun UI *clean* dan fungsionalitas interaktif *modal/dropdown* tanpa *overhead* jQuery.

# Laravel Architecture
- **MVC (Model-View-Controller)** standar Laravel.
- **Form Requests**: Untuk validasi input barang masuk, barang keluar, registrasi user.
- **Middleware**: Memisahkan route untuk `IsAdmin` dan `IsPetugas`.
- **Blade Components**: Reusable UI (card, table, modal, alert).
- **Console Kernel / Scheduler**: Command artisan terenkapsulasi untuk menghitung *Class-Based Storage* yang dieksekusi secara periodik.

# API Requirement
Untuk mendukung fitur Scanner secara asinkron di halaman web:
- `GET /api/scan/{barcode}`: Mengembalikan data JSON berisi `kode_barang`, `nama_barang`, `kategori`, `stok`, `kelas_storage`, `lokasi_rak`.
- Diperlukan setup autentikasi rute API menggunakan Sanctum atau berbasis Session (jika dipanggil dari internal Blade dengan CSRF token).

# Future Scalability Notes
- **Multi-Warehouse**: Menambahkan tabel `warehouses` dan relasinya jika jumlah gudang bertambah.
- **Batch & Expired Tracking**: Jika digunakan untuk *FMCG* (Fast-Moving Consumer Goods), perlu penambahan *Batch ID* dan *Expiry Date*.
- **PWA (Progressive Web App)**: Membuat halaman scanner petugas bisa diakses secara *offline* parsial dan *installable* di Home Screen HP.
