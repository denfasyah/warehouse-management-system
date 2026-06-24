# 📋 Implementation Tasks — WMS Class-Based Storage
**Proyek:** Sistem Manajemen Gudang dengan Metode Class-Based Storage & Barcode
**Stack:** Laravel 12 · PHP 8.3 · MySQL · Blade · Tailwind CSS · Alpine.js
**Status Terakhir Diupdate:** Phase 1 ✅ Selesai

---

## ✅ Phase 1: Foundation & Project Setup — SELESAI

- [x] 1.1 Konfigurasi file `.env` — koneksi MySQL (`warehouse_db`, host `127.0.0.1`, port `3306`)
- [x] 1.2 Setup PHP 8.3 portable di folder `tools/php83/` karena XAMPP masih PHP 8.0
- [x] 1.3 Buat file `start.bat` sebagai shortcut menjalankan server Laravel
- [x] 1.4 Buat database `warehouse_db` via MySQL CLI / phpMyAdmin
- [x] 1.5 Jalankan migration awal bawaan Laravel (`users`, `sessions`, `cache`, `jobs`)
- [x] 1.6 Setup Tailwind CSS via CDN + custom design tokens (warna, font, spacing) di `layouts/app.blade.php`
- [x] 1.7 Setup Alpine.js via CDN (global `x-data` di `<body>`)
- [x] 1.8 Buat struktur folder Views: `layouts/`, `partials/`, `admin/`, `petugas/`, `auth/`
- [x] 1.9 Buat partial `sidebar.blade.php` (collapsible nav, custom scrollbar)
- [x] 1.10 Buat partial `topbar.blade.php` (search, notifikasi, profil)
- [x] 1.11 Buat UI halaman Login (`auth/login.blade.php`)
- [x] 1.12 Buat UI Dashboard Admin (`admin/dashboard.blade.php`) dengan sidebar dropdown lengkap
- [x] 1.13 Buat UI Dashboard Petugas (`petugas/dashboard.blade.php`) dengan sidebar collapsible
- [x] 1.14 Setup route sementara di `web.php` untuk preview (`/`, `/admin`, `/petugas`)

---

## 🔴 Phase 2: Database Schema & Models

> **Tujuan:** Membangun pondasi data yang kuat. Semua fase selanjutnya bergantung pada fase ini.

### 2.1 — Lengkapi Migration: `users` table
- [ ] Tambah kolom `role` (`enum: admin, petugas`) ke migration `0001_01_01_000000_create_users_table.php`
- [ ] Tambah kolom `phone` (`string, nullable`)
- [ ] Tambah kolom `warehouse_sector` (`string, nullable`) — sektor gudang petugas bertugas
- [ ] Jalankan `php artisan migrate:fresh` setelah semua migration siap

### 2.2 — Migration: `categories` table
```
categories
├── id (bigint, PK)
├── name (string) — contoh: "Elektronik", "Mekanik"
├── description (text, nullable)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200609_create_categories_table.php`

### 2.3 — Migration: `locations` table
```
locations
├── id (bigint, PK)
├── zone (string) — "A", "B", "C", "D"
├── rack (string) — "01", "02"
├── bin (string) — "01" (sub-posisi)
├── code (string, unique) — "A-01-01" (generated dari zone+rack+bin)
├── storage_class (enum: fast, medium, slow, general) — kelas CBS zona ini
├── capacity (integer) — kapasitas maks item
├── current_fill (integer, default 0) — isi saat ini
├── is_active (boolean, default true)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200613_create_locations_table.php`

### 2.4 — Migration: `items` table
```
items
├── id (bigint, PK)
├── category_id (FK → categories.id)
├── location_id (FK → locations.id, nullable) — lokasi saat ini
├── name (string) — nama barang
├── sku (string, unique) — kode unik sistem
├── barcode (string, unique) — nilai barcode Code128
├── barcode_image (string, nullable) — path file PNG barcode
├── unit (string) — "pcs", "box", "kg", dll
├── stock (integer, default 0)
├── min_stock (integer, default 5) — stok minimum (trigger alert)
├── storage_class (enum: fast, medium, slow, unclassified, default: unclassified)
├── frequency_score (integer, default 0) — total keluar 30 hari (diisi oleh CBS engine)
├── description (text, nullable)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200617_create_items_table.php`

### 2.5 — Migration: `incoming_goods` table
```
incoming_goods
├── id (bigint, PK)
├── item_id (FK → items.id)
├── user_id (FK → users.id) — petugas yang input
├── location_id (FK → locations.id) — lokasi penempatan
├── quantity (integer)
├── note (text, nullable)
├── received_at (datetime)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200632_create_incoming_goods_table.php`

### 2.6 — Migration: `outgoing_goods` table
```
outgoing_goods
├── id (bigint, PK)
├── item_id (FK → items.id)
├── requested_by (FK → users.id) — petugas pengaju
├── approved_by (FK → users.id, nullable) — admin yang approve
├── location_id (FK → locations.id)
├── quantity (integer)
├── status (enum: pending, approved, rejected, default: pending)
├── reject_reason (text, nullable)
├── note (text, nullable)
├── requested_at (datetime)
├── processed_at (datetime, nullable)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200634_create_outgoing_goods_table.php`

### 2.7 — Migration: `notifications` table
```
notifications (gunakan tabel notifikasi manual — lebih simpel dari Laravel Notifications)
├── id (bigint, PK)
├── user_id (FK → users.id) — penerima notif
├── type (string) — "approval_request", "approved", "rejected", "low_stock"
├── title (string)
├── message (text)
├── data (json, nullable) — payload tambahan (id transaksi, dll)
├── read_at (timestamp, nullable)
├── created_at, updated_at
```
- [ ] Isi file `2026_06_24_200637_create_notifications_table.php`

### 2.8 — Eloquent Models
- [ ] Update `User.php` — tambah fillable, cast role, relasi `incomingGoods()`, `outgoingGoods()`, `notifications()`
- [ ] Buat `Category.php` — relasi `hasMany(Item::class)`
- [ ] Buat `Location.php` — relasi `hasMany(Item::class)`, `hasMany(IncomingGood::class)`, `hasMany(OutgoingGood::class)`, accessor `fullCode()`
- [ ] Buat `Item.php` — relasi `belongsTo(Category::class)`, `belongsTo(Location::class)`, `hasMany(IncomingGood::class)`, `hasMany(OutgoingGood::class)`, accessor `isLowStock()`
- [ ] Buat `IncomingGood.php` — relasi ke `Item`, `User`, `Location`
- [ ] Buat `OutgoingGood.php` — relasi ke `Item`, `User` (requester+approver), `Location`
- [ ] Buat `Notification.php` — relasi ke `User`, scope `unread()`

### 2.9 — Database Seeders
- [ ] `DatabaseSeeder.php` — orkestrasi urutan seeder
- [ ] `UserSeeder.php` — 1 Admin (`admin@wms.com` / `password`), 2 Petugas
- [ ] `CategorySeeder.php` — 5 kategori: Elektronik, Mekanikal, Kimia, Peralatan, Umum
- [ ] `LocationSeeder.php` — minimal 20 lokasi: zona A (fast), B (medium), C (slow), D (general), format A-01-01 s/d A-05-02
- [ ] `ItemSeeder.php` — 20 item dummy dengan barcode, SKU, stok acak, dan kategori

### 2.10 — Jalankan & Verifikasi
- [ ] Jalankan: `php artisan migrate:fresh --seed`
- [ ] Verifikasi di phpMyAdmin: semua tabel terbuat dengan kolom yang benar
- [ ] Pastikan tidak ada foreign key constraint error

---

## 🔴 Phase 3: Autentikasi & Otorisasi

> **Tujuan:** Sistem login yang aman dengan pembatasan akses berdasarkan role.

### 3.1 — AuthController
- [ ] Buat `AuthController.php` dengan method: `showLogin()`, `login()`, `logout()`
- [ ] Method `login()`: validasi input → cek kredensial `Auth::attempt()` → redirect berdasarkan `role`
  - `admin` → redirect ke `/admin/dashboard`
  - `petugas` → redirect ke `/petugas/dashboard`
- [ ] Method `logout()`: `Auth::logout()` → redirect ke `/login`

### 3.2 — RoleMiddleware
- [ ] Buat `app/Http/Middleware/RoleMiddleware.php`
  ```php
  // Cek: Auth::check() && Auth::user()->role === $role
  // Jika tidak sesuai: abort(403) atau redirect dengan pesan
  ```
- [ ] Daftarkan di `bootstrap/app.php` sebagai named middleware: `'role'`

### 3.3 — Routing (`routes/web.php`)
- [ ] Route publik: `GET /login`, `POST /login`, `POST /logout`
- [ ] Route group Admin: prefix `/admin`, middleware `['auth', 'role:admin']`
  - Dashboard, Master Data (barang, kategori, lokasi, user), CBS, Persetujuan, Laporan, Pengaturan
- [ ] Route group Petugas: prefix `/petugas`, middleware `['auth', 'role:petugas']`
  - Dashboard, Barang Masuk, Barang Keluar, Scan Barcode, Storage, Riwayat

### 3.4 — Blade Integration
- [ ] Sambungkan form Login ke `AuthController@login` (action, method POST, CSRF)
- [ ] Tampilkan error validasi di form login (`@error`)
- [ ] Ganti semua `href="#"` di sidebar dengan route yang benar sesuai role
- [ ] Tampilkan nama user dan role dari `Auth::user()` di topbar dan sidebar profile card

---

## 🟡 Phase 4: Master Data — CRUD Admin

> **Tujuan:** Admin dapat mengelola semua data master sistem.

### 4.1 — CRUD Kategori Barang
- [ ] `CategoryController` dengan `index()`, `store()`, `update()`, `destroy()`
- [ ] View `admin/categories/index.blade.php` — tabel + modal tambah/edit + tombol hapus
- [ ] Validasi: `name` required, unique (kecuali dirinya sendiri saat update)
- [ ] Guard: tidak bisa hapus kategori jika masih ada barang yang menggunakan

### 4.2 — CRUD Lokasi Penyimpanan
- [ ] `LocationController` dengan full CRUD
- [ ] View `admin/locations/index.blade.php` — tabel dengan kolom: Kode, Zona, Kelas CBS, Kapasitas, Isi, Status
- [ ] Form tambah/edit: zona, rak, bin → auto-generate `code` (A-01-01)
- [ ] Dropdown `storage_class`: Fast Moving / Medium Moving / Slow Moving / General
- [ ] Validasi: kode harus unik, kapasitas > 0
- [ ] Tampilkan persentase pengisian dengan progress bar

### 4.3 — CRUD Data Barang
- [ ] `ItemController` dengan full CRUD
- [ ] View `admin/items/index.blade.php` — tabel dengan search & filter (kategori, kelas CBS, stok rendah)
- [ ] View `admin/items/create.blade.php` / `edit.blade.php` — form lengkap:
  - Nama, SKU (auto-generate bisa), Kategori, Satuan, Stok Awal, Stok Minimum, Lokasi, Deskripsi
- [ ] Saat `store()`: generate barcode otomatis dengan format `WMS-{kategori_kode}-{random_6_digit}`
- [ ] Guard: validasi stok tidak boleh negatif

### 4.4 — Generate & Cetak Barcode
- [ ] Install library: `composer require milon/barcode`
- [ ] `BarcodeController@generate($item_id)`: ambil item, generate SVG/PNG barcode Code128, simpan ke `storage/app/public/barcodes/`
- [ ] `BarcodeController@print($item_id)`: tampilkan view siap cetak
- [ ] View `admin/items/print-barcode.blade.php`: layout A4/label, tampilkan barcode image + nama barang + SKU + lokasi
- [ ] Route: `GET /admin/items/{id}/barcode/generate`, `GET /admin/items/{id}/barcode/print`
- [ ] Tombol "Generate" dan "Cetak" di halaman detail/list barang

### 4.5 — Manajemen User (Petugas)
- [ ] `UserController` hanya untuk Admin
- [ ] View `admin/users/index.blade.php` — tabel daftar user + toggle aktif/nonaktif
- [ ] Form tambah/edit: nama, email, password, role (hanya petugas yang bisa dibuat di sini), sektor gudang
- [ ] Validasi: email unik, password min 8 karakter

---

## 🟡 Phase 5: Transaksi Gudang — Petugas

> **Tujuan:** Petugas dapat melakukan operasional gudang sehari-hari.

### 5.1 — Barang Masuk
- [ ] `IncomingGoodController@index()` — riwayat barang masuk hari ini / semua
- [ ] `IncomingGoodController@create()` — form input: cari barang (by SKU/barcode/nama), pilih lokasi, jumlah, catatan
- [ ] `IncomingGoodController@store()`:
  - Validasi: item ada, lokasi aktif dan bukan kapasitas penuh, qty > 0
  - DB Transaction: INSERT `incoming_goods` + UPDATE `items.stock += qty` + UPDATE `locations.current_fill += qty`
  - Return success + redirect
- [ ] View `petugas/incoming/create.blade.php` — form dengan live search barang (Alpine.js)

### 5.2 — Scanner Barcode (Kamera HP)
- [ ] View `petugas/scanner/index.blade.php`:
  - Integrasikan library JS: `html5-qrcode` via CDN
  - UI: area preview kamera, status scan, hasil scan
- [ ] Saat barcode berhasil di-scan → fetch AJAX ke endpoint
- [ ] `ScanController@lookup(Request $request)` — API endpoint:
  - `GET /petugas/scan/lookup?barcode={value}`
  - Return JSON: `{ found: true, item: { id, name, sku, stock, location, storage_class } }`
  - Atau: `{ found: false, message: 'Barcode tidak ditemukan' }`
- [ ] Setelah item ditemukan: tampilkan kartu info barang, muncul pilihan "Barang Masuk" atau "Barang Keluar"

### 5.3 — Barang Keluar (Request Approval)
- [ ] `OutgoingGoodController@create()` — form: pilih/scan barang, pilih lokasi, qty, catatan, tujuan pengiriman
- [ ] `OutgoingGoodController@store()`:
  - Validasi: qty tidak melebihi stok tersedia
  - INSERT `outgoing_goods` dengan `status = 'pending'`
  - **TIDAK langsung kurangi stok** — harus menunggu approval Admin
  - Buat notifikasi untuk Admin: "Ada permintaan Barang Keluar baru dari {nama_petugas}"
- [ ] View `petugas/outgoing/index.blade.php` — riwayat request + status (pending/approved/rejected)
- [ ] View `petugas/outgoing/create.blade.php` — form request keluar

---

## 🟡 Phase 6: Approval Workflow & Notifikasi

> **Tujuan:** Admin dapat mereview dan memutuskan permintaan barang keluar.

### 6.1 — Halaman Approval Admin
- [ ] `ApprovalController@index()` — tampilkan semua request `pending`
- [ ] View `admin/approvals/index.blade.php`:
  - Tabel: No, Tanggal Request, Barang, Qty, Petugas, Stok Saat Ini, Catatan, Aksi
  - Tombol "Approve ✓" dan "Reject ✗" per baris
- [ ] View `admin/approvals/show.blade.php` — detail lengkap satu request

### 6.2 — Logika Approve
- [ ] `ApprovalController@approve($id)`:
  - **Gunakan DB Transaction**
  - Cek: status masih `pending`, stok masih cukup
  - UPDATE `outgoing_goods.status = 'approved'`, set `approved_by`, `processed_at`
  - UPDATE `items.stock -= qty`
  - UPDATE `locations.current_fill -= qty`
  - INSERT notifikasi untuk Petugas: "Permintaan Barang Keluar Anda disetujui"
  - Return redirect dengan flash success

### 6.3 — Logika Reject
- [ ] `ApprovalController@reject($id, Request $request)`:
  - UPDATE `outgoing_goods.status = 'rejected'`, simpan `reject_reason`
  - INSERT notifikasi untuk Petugas: "Permintaan ditolak: {alasan}"
  - Return redirect dengan flash warning

### 6.4 — Sistem Notifikasi Real-time (Sederhana)
- [ ] `NotificationController@index()` — ambil semua notif user yang login, mark as read
- [ ] `NotificationController@markRead($id)` — tandai 1 notif sebagai dibaca
- [ ] `NotificationController@markAllRead()` — tandai semua as dibaca
- [ ] Topbar: tampilkan badge count unread dari `Notification::where('user_id', auth()->id())->whereNull('read_at')->count()`
- [ ] Dropdown notifikasi (Alpine.js + Blade) — tampilkan 5 notif terbaru
- [ ] Polling ringan: Alpine.js `setInterval` fetch JSON count notif setiap 30 detik (tanpa WebSocket)

---

## 🟠 Phase 7: Class-Based Storage Engine

> **Tujuan:** Implementasi metode inti penelitian skripsi — klasifikasi otomatis barang berdasarkan frekuensi keluar.

### 7.1 — Artisan Command: Hitung Kelas
- [ ] Buat: `php artisan make:command CalculateStorageClass`
- [ ] File: `app/Console/Commands/CalculateStorageClass.php`
- [ ] Algoritma dalam `handle()`:
  ```
  1. Ambil semua item
  2. Per item: hitung total qty keluar dalam 30 hari terakhir dari outgoing_goods (status approved)
  3. Simpan ke items.frequency_score
  4. Tentukan ambang batas (threshold) dari settings:
     - fast_threshold: misal >= 50
     - medium_threshold: misal >= 10 dan < 50
     - slow_threshold: misal < 10
  5. Update items.storage_class sesuai hasil
  6. Tampilkan summary di console
  ```
- [ ] Register command di `routes/console.php` atau `app/Console/Kernel.php`

### 7.2 — Model Settings (Threshold CBS)
- [ ] Buat tabel `settings`: `key` (string, unique), `value` (string), `description`
- [ ] Seeder: insert default threshold (`fast_threshold=50`, `medium_threshold=10`)
- [ ] `SettingController@index()` / `update()` — form edit threshold di Admin
- [ ] View `admin/settings/index.blade.php` — form input threshold dengan penjelasan

### 7.3 — Halaman Klasifikasi Barang
- [ ] View `admin/cbs/classification.blade.php`:
  - Tabel semua barang dengan kolom: Nama, SKU, Frekuensi (30 hari), Kelas Saat Ini, Kelas Baru (preview)
  - Filter by kelas: Fast / Medium / Slow / Unclassified
  - Tombol "Jalankan Kalkulasi Sekarang" → memanggil `CalculateStorageClass` via HTTP request
  - Timestamp "Terakhir dikalkulasi: ..."
- [ ] Route: `POST /admin/cbs/recalculate` → jalankan command + redirect

### 7.4 — Halaman Mapping Storage (Visualisasi Rak)
- [ ] View `admin/cbs/mapping.blade.php`:
  - Visualisasi grid rak: setiap sel = satu lokasi
  - Warna sel berdasarkan storage_class: merah (fast), kuning (medium), hijau (slow), abu (general)
  - Tampilkan persentase pengisian di setiap sel
  - Klik sel → popup detail lokasi dan barang di dalamnya
- [ ] Implementasikan dengan Alpine.js + data dari controller

### 7.5 — Rekomendasi Pindah Rak
- [ ] Di halaman Klasifikasi: jika `items.storage_class` tidak sesuai dengan `locations.storage_class` tempat barang berada → tampilkan badge "Perlu Relokasi" dengan warna oranye
- [ ] Halaman detail item: tampilkan "Lokasi Saat Ini: A-01-01 (Fast Zone)" dan "Kelas Barang: Slow" → rekomendasi pindah ke zona C

---

## 🟢 Phase 8: Dashboard Dinamis & Pelaporan

> **Tujuan:** Data di dashboard tersambung ke database, laporan bisa diexport.

### 8.1 — Dashboard Admin — Data Real
- [ ] `AdminDashboardController@index()`:
  - Query: total item, count fast/medium/slow, masuk hari ini, keluar hari ini (approved)
  - Query: data 7 hari terakhir untuk grafik (masuk vs keluar per hari)
  - Query: kapasitas penyimpanan per zona (fill vs kapasitas)
  - Pass semua data ke view sebagai compact variables
- [ ] Integrasikan **Chart.js** via CDN untuk grafik line/bar
- [ ] Update blade: ganti semua angka dummy dengan `{{ $totalItems }}`, `{{ $fastMovingCount }}`, dll

### 8.2 — Dashboard Petugas — Data Real
- [ ] `PetugasDashboardController@index()`:
  - Query: barang masuk hari ini (oleh petugas login), barang keluar hari ini, total scan (incoming+outgoing)
  - Query: 10 aktivitas terbaru (gabungan incoming + outgoing)
- [ ] Update blade view petugas

### 8.3 — Halaman Laporan
- [ ] `ReportController` dengan methods:
  - `stockReport()` — semua barang + stok + kelas + lokasi. Filter: kategori, kelas, stok rendah
  - `incomingReport()` — riwayat masuk. Filter: tanggal, petugas, barang
  - `outgoingReport()` — riwayat keluar. Filter: tanggal, status, petugas
  - `storageReport()` — kapasitas semua lokasi + persentase pengisian + rekomendasi
- [ ] Views: `admin/reports/stock.blade.php`, `incoming.blade.php`, `outgoing.blade.php`, `storage.blade.php`
- [ ] Setiap halaman: tabel data + filter form + tombol "Export PDF"

### 8.4 — Export PDF
- [ ] Install: `composer require barryvdh/laravel-dompdf`
- [ ] Publish config: `php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"`
- [ ] Buat view PDF per laporan: `admin/reports/pdf/stock-pdf.blade.php` (clean, no sidebar)
- [ ] Method di controller: `exportPdf()` — `PDF::loadView(...)->download('laporan-stok.pdf')`
- [ ] Tambah tombol "Export PDF" di setiap halaman laporan

---

## 🔵 Phase 9: UI Polish, Testing & Hardening

> **Tujuan:** Pastikan tidak ada bug, UI responsif sempurna, dan siap presentasi.

### 9.1 — Responsivitas Mobile
- [ ] Test semua halaman di viewport mobile (375px) dan tablet (768px)
- [ ] Pastikan tabel menggunakan `overflow-x-auto` dan `min-w-[xxx]` agar tidak patah di mobile
- [ ] Pastikan form input tidak terlalu kecil di mobile (min height input 44px)
- [ ] Pastikan scanner barcode bekerja di browser HP (test di Chrome Android)
- [ ] Pastikan sidebar mobile bisa dibuka/tutup dengan smooth

### 9.2 — Validasi & Error Handling
- [ ] Semua form Controller menggunakan `$request->validate([...])`
- [ ] Tampilkan pesan error di bawah setiap field dengan `@error('field')`
- [ ] Semua route yang bisa di-akses langsung (without form) dikembalikan dengan 404 jika tidak ada
- [ ] Semua DB Transaction menggunakan `try/catch` dan `DB::rollBack()` jika gagal
- [ ] Pastikan tidak ada `N+1 query problem` — gunakan `with(['relation'])` di semua controller

### 9.3 — Security Hardening
- [ ] Semua form menggunakan `@csrf`
- [ ] Update/delete menggunakan `@method('PUT')` / `@method('DELETE')`
- [ ] Semua route dilindungi middleware `auth` + `role`
- [ ] Input yang ditampilkan ke HTML selalu melalui `{{ }}` (bukan `{!! !!}`) kecuali memang perlu render HTML
- [ ] Barcode image tersimpan di `storage/app/public/barcodes/` dan diakses via `Storage::url()`
- [ ] Pastikan `php artisan storage:link` sudah dijalankan

### 9.4 — End-to-End Workflow Test
- [ ] ✅ Alur 1 (Barang Masuk): Login Petugas → Scan Barcode → Pilih Masuk → Isi Form → Submit → Cek stok bertambah di Admin
- [ ] ✅ Alur 2 (Barang Keluar + Approval): Login Petugas → Request Keluar → Login Admin → Approve → Cek stok berkurang
- [ ] ✅ Alur 3 (CBS): Admin → Jalankan kalkulasi → Lihat klasifikasi berubah → Lihat rekomendasi relokasi
- [ ] ✅ Alur 4 (Laporan PDF): Admin → Laporan Stok → Export PDF → PDF terdownload dengan data benar
- [ ] ✅ Alur 5 (Reject): Admin → Reject request → Petugas dapat notifikasi → Status berubah ke rejected

### 9.5 — Optimasi Akhir
- [ ] Jalankan `php artisan config:cache` dan `php artisan route:cache` sebelum demo
- [ ] Pastikan semua gambar/asset ada
- [ ] Cek semua link navigasi sidebar sudah mengarah ke route yang benar
- [ ] Review seluruh UI — font konsisten, warna konsisten, spacing konsisten

---

## 📌 Urutan Prioritas Pengerjaan

```
Phase 2 (Database) → Phase 3 (Auth) → Phase 4 (Master Data) → Phase 5 (Transaksi)
→ Phase 6 (Approval) → Phase 7 (CBS Engine) → Phase 8 (Dashboard+Laporan) → Phase 9 (Polish)
```

**Estimasi Waktu:**
| Phase | Estimasi |
|---|---|
| Phase 2 | 2–3 jam |
| Phase 3 | 1–2 jam |
| Phase 4 | 4–6 jam |
| Phase 5 | 3–4 jam |
| Phase 6 | 2–3 jam |
| Phase 7 | 3–5 jam |
| Phase 8 | 3–4 jam |
| Phase 9 | 2–3 jam |
| **Total** | **~22–30 jam kerja** |

---

## ❓ Pertanyaan Terbuka

1. **Nama perusahaan di UI** — saat ini memakai "PT. IndoOne Sentosa Indah Abadi". Apakah ini nama fiktif untuk skripsi atau akan diganti nama nyata?
2. **Threshold CBS default** — saya pakai contoh Fast ≥ 50, Medium ≥ 10. Apakah nilai ini sudah sesuai dengan referensi skripsi Anda, atau perlu disesuaikan?
3. **Format SKU barang** — saat ini rencana auto-generate: `WMS-{KODE_KAT}-{6_DIGIT_RANDOM}`. Apakah ada format khusus yang diinginkan?
4. **Cetak barcode** — apakah perlu bisa cetak banyak barcode sekaligus (batch print, misal 10 item), atau cukup satu per satu?
5. **Grafik Dashboard** — apakah perlu grafik yang lebih spesifik (selain line chart masuk vs keluar)? Misalnya pie chart distribusi CBS?
