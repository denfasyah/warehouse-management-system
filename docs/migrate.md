# 📦 Panduan Migrasi Proyek WMS ke Laptop Baru

Dokumen ini berisi panduan lengkap **langkah demi langkah** untuk memindahkan dan menjalankan proyek **Warehouse Management System (WMS)** di laptop lain yang baru/fresh (belum ada PHP, Composer, dll).

---

## ✅ Ringkasan Software yang Perlu Diunduh

| Software | Kegunaan | Link Download |
|----------|----------|---------------|
| XAMPP | Menyediakan PHP & MySQL lokal | https://www.apachefriends.org |
| Composer | Manajemen dependensi PHP (Laravel) | https://getcomposer.org/download |
| Node.js (LTS) | Untuk build frontend assets | https://nodejs.org |
| VS Code | Code editor | https://code.visualstudio.com |

---

## 📋 Langkah 1 – Install Software Pendukung

### A. Install XAMPP
1. Buka browser, unduh XAMPP di: **https://www.apachefriends.org**
2. Pilih versi yang menggunakan **PHP 8.2** atau **PHP 8.3**
3. Jalankan file installer (`xampp-windows-x64-xxx-installer.exe`)
4. Klik **Next** terus hingga selesai (biarkan direktori default di `C:\xampp`)
5. Setelah selesai, buka **XAMPP Control Panel** dan klik **Start** pada:
   - Modul **Apache** → tunggu indikator hijau
   - Modul **MySQL** → tunggu indikator hijau

### B. Install Composer
1. Unduh installer di: **https://getcomposer.org/download**
2. Klik **Composer-Setup.exe**
3. Saat diminta lokasi `php.exe`, arahkan ke:
   ```
   C:\xampp\php\php.exe
   ```
4. Klik **Next** dan selesaikan instalasi
5. Verifikasi berhasil: buka Command Prompt (`cmd`), ketik `composer --version`

### C. Install Node.js
1. Unduh di: **https://nodejs.org** → pilih versi **LTS**
2. Jalankan installer, klik **Next** hingga selesai
3. Verifikasi: buka `cmd`, ketik `node --version` dan `npm --version`

---

## 📋 Langkah 2 – Pindahkan Folder Proyek

### Pilihan A: Lewat Flashdisk / Hard Disk Eksternal
1. Di laptop lama, **kompres** folder `warehouse` menjadi file `.zip`
   - Klik kanan folder `warehouse` → **Send to** → **Compressed (zipped) folder**
2. Salin file `.zip` ke flashdisk/hard disk
3. Di laptop baru, tempel (paste) file `.zip` ke lokasi yang diinginkan (misal: `Desktop`)
4. Klik kanan file `.zip` → **Extract All** → selesai

### Pilihan B: Lewat Google Drive
1. Di laptop lama, upload folder `warehouse` (dalam format `.zip`) ke Google Drive
2. Di laptop baru, login Google Drive dan unduh file tersebut
3. Ekstrak file `.zip` di lokasi yang diinginkan

> **⚠️ Penting:** Lokasi folder **tidak boleh mengandung spasi** di nama path-nya.
> Contoh aman: `C:\Projects\warehouse` atau `D:\WMS\warehouse`

---

## 📋 Langkah 3 – Buka Proyek di VS Code

1. Buka aplikasi **Visual Studio Code**
2. Pilih menu **File** → **Open Folder...**
3. Arahkan dan pilih folder `warehouse` yang sudah diekstrak
4. Klik **Select Folder**

---

## 📋 Langkah 4 – Buat Database di XAMPP

1. Pastikan **XAMPP Control Panel** sudah berjalan dan MySQL berstatus **Running (hijau)**
2. Buka browser, akses: **http://localhost/phpmyadmin/**
3. Di panel kiri, klik tombol **New**
4. Pada kolom **Database name**, ketik:
   ```
   warehouse_db
   ```
5. Klik tombol **Create**

---

## 📋 Langkah 5 – Konfigurasi File `.env`

1. Di VS Code, buka file `.env` yang ada di **root folder proyek** (bukan di subfolder)
2. Temukan bagian konfigurasi database dan pastikan isinya seperti ini:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=warehouse_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > Password dibiarkan **kosong** karena XAMPP tidak menggunakan password untuk user `root` secara default.
3. Simpan file (`Ctrl + S`)

---

## 📋 Langkah 6 – Instalasi Dependensi via Terminal VS Code

1. Di VS Code, buka terminal baru: menu **Terminal** → **New Terminal** (atau tekan `` Ctrl + ` ``)
2. Pastikan terminal berada di dalam folder proyek (terlihat path folder `warehouse` di baris prompt)

### A. Install dependensi PHP (Composer)
```bash
composer install
```
> Tunggu hingga proses selesai. Ini akan mengunduh semua library Laravel. Bisa memakan waktu 2-5 menit tergantung koneksi internet.

### B. Install dependensi Frontend (NPM)
```bash
npm install
```

### C. Generate Application Key
```bash
php artisan key:generate
```

### D. Jalankan Migrasi & Isi Data Sampel
```bash
php artisan migrate:fresh --seed
```
> Perintah ini akan membuat semua tabel database dan mengisi data sampel sesuai skripsi secara otomatis (Engine Oil, Battery, dll. beserta riwayat transaksi 30 hari).

---

## 📋 Langkah 7 – Jalankan Aplikasi

### Pilihan A: Klik ganda `start.bat` (Mudah)
- Cukup klik dua kali file `start.bat` yang ada di dalam folder proyek
- Sebuah jendela Command Prompt hitam akan muncul, artinya server sudah berjalan

### Pilihan B: Lewat Terminal VS Code
```bash
php artisan serve
```

### Akses di Browser
Buka browser dan ketik alamat:
```
http://127.0.0.1:8000
```

---

## 🔑 Data Login Akun

| Peran | Email | Password |
|-------|-------|----------|
| **Admin** | `admin@wms.com` | `password` |
| **Petugas 1** | `agus@wms.com` | `password` |
| **Petugas 2** | `siti@wms.com` | `password` |

---

## ❓ Troubleshooting Umum

| Masalah | Solusi |
|---------|--------|
| `composer: command not found` | Restart laptop setelah instal Composer, lalu coba lagi |
| Error koneksi database | Pastikan MySQL di XAMPP sudah **Start (hijau)** |
| Halaman error 500 | Pastikan file `.env` sudah dikonfigurasi dan jalankan `php artisan key:generate` |
| Port 8000 sudah dipakai | Jalankan `php artisan serve --port=8080` dan akses `http://127.0.0.1:8080` |
| `npm: command not found` | Restart laptop setelah instal Node.js, lalu coba lagi |
