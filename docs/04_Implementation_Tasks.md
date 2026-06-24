# Implementation Tasks

Berikut adalah tahapan (step-by-step) untuk mengimplementasikan proyek ini dari awal hingga selesai:

## Phase 1: Foundation & Setup
- [ ] 1.1 Konfigurasi Database (update `.env` untuk MySQL).
- [ ] 1.2 Konfigurasi Tailwind CSS dan Alpine.js di aplikasi.
- [ ] 1.3 Setup struktur folder UI komponen (layouts, partials, components).

## Phase 2: Database & Models
- [ ] 2.1 Buat *Migration* untuk tabel `users`, `categories`, `locations`.
- [ ] 2.2 Buat *Migration* untuk tabel `items`, `incoming_goods`, `outgoing_goods`.
- [ ] 2.3 Buat *Migration* untuk tabel `notifications`.
- [ ] 2.4 Setup *Eloquent Models* dan *Relationships* untuk semua tabel.
- [ ] 2.5 Buat *Database Seeders* (Akun Admin, Petugas, Kategori dummy, Rak dummy).

## Phase 3: Autentikasi & Otorisasi
- [ ] 3.1 Integrasikan sistem Auth bawaan Laravel (atau Breeze standar).
- [ ] 3.2 Buat Custom Middleware `RoleMiddleware` untuk memproteksi akses Admin dan Petugas.
- [ ] 3.3 Buat struktur Routing di `web.php` dikelompokkan berdasarkan Role.

## Phase 4: Master Data & Admin Features
- [ ] 4.1 CRUD Data Kategori (Controller, Views).
- [ ] 4.2 CRUD Data Lokasi/Rak (Controller, Views).
- [ ] 4.3 CRUD Data Barang.
- [ ] 4.4 Integrasikan library `php-barcode-generator` saat input/edit Barang.
- [ ] 4.5 Fitur Cetak Barcode (Tampilan siap *print* untuk dilabeli di rak/barang).

## Phase 5: Transaksi Gudang (Petugas)
- [ ] 5.1 Buat Form Input Barang Masuk (Controller, Views). Otomatis tambah stok.
- [ ] 5.2 Buat Halaman Scanner Barcode (integrasi library JS HTML5-QRCode).
- [ ] 5.3 Buat API Endpoint untuk meretrieve data hasil scan barcode.
- [ ] 5.4 Buat Form Input Barang Keluar dari hasil scan. Otomatis masuk ke *Pending Approval*.

## Phase 6: Approval Workflow & Notifications
- [ ] 6.1 Halaman Approval di sisi Admin (Daftar *Pending Request*).
- [ ] 6.2 Logika *Approve* (mengurangi stok `items`, update status) menggunakan DB Transaction.
- [ ] 6.3 Logika *Reject* (update status saja).
- [ ] 6.4 Sistem Notifikasi di panel atas (Header/Navbar) saat status berubah.

## Phase 7: Class-Based Storage Engine
- [ ] 7.1 Buat Artisan Command (`php artisan storage:calculate-class`).
- [ ] 7.2 Implementasi algoritma agregasi frekuensi pengeluaran barang dalam 30 hari.
- [ ] 7.3 Logika update field `storage_class` di tabel `items`.
- [ ] 7.4 (Opsional) Rekomendasi pindah rak di antarmuka Admin jika barang berubah kelas.

## Phase 8: Dashboard & Pelaporan
- [ ] 8.1 Bangun UI Dashboard Admin (Cards, Charts).
- [ ] 8.2 Bangun UI Dashboard Petugas (Quick Actions).
- [ ] 8.3 Buat halaman Laporan Stok dan Riwayat Transaksi.
- [ ] 8.4 Integrasi `dompdf` untuk mengekspor data laporan ke dalam format PDF.

## Phase 9: Testing & UI Polish
- [ ] 9.1 Pengecekan responsivitas (khususnya scanner HP petugas).
- [ ] 9.2 Refactoring kode, memastikan UI rapi dan modern.
- [ ] 9.3 Uji coba *End-to-End* workflow dari barang masuk $\rightarrow$ scan keluar $\rightarrow$ approval $\rightarrow$ laporan.
