# Analisa Kebutuhan (Requirements Analysis) - Warehouse Management System

Dokumen ini berisi analisis kebutuhan fungsional dan non-fungsional berdasarkan struktur proyek aplikasi Warehouse Management System (WMS).

## 1. Kebutuhan Fungsional (Functional Requirements)

Sistem ini memiliki dua peran pengguna utama, yaitu **Admin** dan **Petugas** (Gudang). Berikut adalah kebutuhan fungsional yang harus dipenuhi oleh sistem:

### 1.1 Manajemen Pengguna dan Akses
* **Admin** dapat mengelola data pengguna (User Controller), seperti menambah, mengubah, dan menghapus (CRUD) akun pengguna.
* Sistem harus menyediakan mekanisme login, logout, dan manajemen sesi.
* Sistem membatasi akses berdasarkan hak akses/peran (Role-Based Access Control) antara Admin dan Petugas.

### 1.2 Manajemen Data Master (Admin)
* **Kategori (Category):** Admin dapat mengelola kategori barang.
* **Lokasi (Location):** Admin dapat mengelola titik lokasi penyimpanan dalam gudang.
* **Barang (Item):** Admin dapat mengelola data induk barang, termasuk informasi dimensi, berat, dan relasinya dengan lokasi.

### 1.3 Manajemen Operasional Gudang (Petugas & Admin)
* **Barang Masuk (Incoming Goods):** Petugas dapat mencatat barang yang masuk ke dalam gudang, menentukan lokasi awal, dan mencatat kuantitas.
* **Barang Keluar (Outgoing Goods):** Petugas dapat mencatat pengeluaran barang dari gudang menuju tujuan tertentu.
* **Tugas Relokasi (Relocation Task):** Petugas dapat melihat dan melaksanakan tugas pemindahan barang dari satu lokasi ke lokasi lain di dalam gudang.
* **Sistem Barcode & Scanner:** 
    * Admin dapat membuat/mencetak barcode untuk barang (Barcode Controller).
    * Petugas dapat menggunakan fitur scanner untuk mempercepat proses identifikasi barang saat masuk, keluar, atau direlokasi (Scanner Controller).
* **CBS (Cube Based Storage / Capacity Management):** Sistem dapat mengelola kapasitas penyimpanan berdasarkan ruang/dimensi (CBS Controller).

### 1.4 Persetujuan dan Notifikasi
* **Persetujuan (Approval):** Admin dapat meninjau dan menyetujui transaksi (misal: barang keluar atau relokasi) yang diajukan oleh petugas (Approval Controller).
* **Notifikasi:** Sistem dapat memberikan notifikasi kepada pengguna terkait tugas baru, peringatan stok, atau status persetujuan (Notification).

### 1.5 Pengaturan (Settings)
* **Pengaturan Sistem:** Admin dapat mengubah konfigurasi dan parameter dasar aplikasi (Setting Controller).


## 2. Kebutuhan Non-Fungsional (Non-Functional Requirements)

### 2.1 Keamanan (Security)
* Sistem harus melindungi data dari serangan umum web (CSRF, XSS, SQL Injection) sesuai dengan standar keamanan Laravel.
* Password pengguna harus dienkripsi secara aman menggunakan hashing (misalnya bcrypt).
* Akses rute (API/Web) harus dibatasi sesuai dengan middleware peran (Admin vs Petugas).

### 2.2 Kinerja (Performance)
* Sistem harus dapat menangani proses scanning barcode secara responsif tanpa jeda waktu yang signifikan (latency di bawah 2 detik per scan).
* Sistem harus optimal dalam menampilkan list data barang, terutama jika jumlah item dan log mutasi barang (masuk/keluar/relokasi) semakin membesar (menggunakan pagination dan index database yang tepat).

### 2.3 Ketersediaan (Availability) dan Keandalan (Reliability)
* Sistem dirancang beroperasi dengan target *uptime* yang tinggi untuk mendukung aktivitas operasional harian gudang.
* Terdapat penanganan kesalahan (error handling) yang informatif agar pengguna tidak mengalami kebingungan jika terjadi gagal sistem, dan *transaction rollback* untuk memastikan integritas data saat transaksi gagal di pertengahan proses.

### 2.4 Kemudahan Penggunaan (Usability)
* Antarmuka (UI) harus intuitif bagi petugas gudang agar mudah digunakan pada perangkat mobile atau tablet, khususnya untuk fitur Scanner.
* Fitur pencarian dan filter harus tersedia pada setiap modul data master dan transaksi untuk memudahkan penemuan data.

### 2.5 Pemeliharaan (Maintainability)
* Sistem dikembangkan menggunakan kerangka kerja Laravel (v10) dengan arsitektur MVC (Model-View-Controller) untuk memudahkan pembaruan fitur di masa depan.
* Struktur database disusun secara terelasi dan tereferensi agar perubahan pada suatu *entity* tidak merusak *entity* lain yang terhubung.
