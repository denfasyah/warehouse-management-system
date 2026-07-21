# Panduan Hosting / Deployment Sistem Manajemen Gudang (Laravel 10)

Dokumen ini berisi panduan dan saran untuk melakukan *hosting* atau *deployment* proyek Sistem Manajemen Gudang (Warehouse Management System) Anda. Karena proyek ini diperuntukkan sebagai tugas akhir / skripsi, panduan ini disusun agar mudah dipahami, murah, dan praktis.

---

## 1. Shared Hosting vs VPS: Mana yang Dipilih?

Untuk keperluan **Skripsi / Tugas Akhir**, sangat disarankan menggunakan **Shared Hosting** dibandingkan VPS (Virtual Private Server).

*   **Shared Hosting (Rekomendasi):**
    *   **Kelebihan:** Sangat mudah digunakan (tinggal klik-klik di panel), tidak perlu keahlian sistem admin Linux, harga relatif lebih murah, sudah termasuk bantuan *Customer Support*, dan sudah disediakan SSL gratis.
    *   **Kekurangan:** Performa terbagi dengan pengguna lain, akses terminal terbatas.
    *   **Cocok untuk:** Aplikasi skripsi yang *traffic* (pengunjung)-nya masih sedikit (hanya dosen penguji atau uji coba skala kecil).
*   **VPS (Virtual Private Server):**
    *   **Kelebihan:** Performa murni milik Anda, bebas menginstal aplikasi atau servis apapun di *server*.
    *   **Kekurangan:** Anda harus mengatur semuanya dari nol (install Nginx/Apache, PHP, MySQL, SSL, dll) via layar hitam (Terminal/Command Line). Sangat pusing jika belum terbiasa.
    *   **Cocok untuk:** Aplikasi skala besar / *startup* beneran.

**Kesimpulan:** Gunakan **Shared Hosting**.

---

## 2. Rekomendasi Penyedia Hosting

Karena Anda menyebutkan **Hostinger**, itu adalah pilihan yang sangat tepat dan sangat populer di kalangan mahasiswa di Indonesia.
*   **Paket yang disarankan:** Paket **Premium Web Hosting** atau **Business Web Hosting** di Hostinger sudah sangat lebih dari cukup.
*   Penyedia lain yang juga bagus dan murah untuk Laravel: Niagahoster, DomaiNesia, atau JagoanHosting.

---

## 3. Persiapan Proyek Sebelum Hosting (Lokal)

Sebelum mengunggah *file* ke hosting, Anda perlu menyiapkan konfigurasi proyek di komputer Anda (Lokal):

1.  **Pastikan Kode Bersih dan Stabil:** Pastikan tidak ada *error* saat dijalankan di lokal.
2.  **Export Database Anda:**
    *   Buka phpMyAdmin di lokal (biasanya `localhost/phpmyadmin`).
    *   Pilih database proyek Anda (misal: `warehouse`).
    *   Klik tab **Export** -> Format: **SQL** -> Klik **Go** / **Export**.
    *   Simpan file `.sql` tersebut (misal `warehouse.sql`). Ini akan diimpor ke hosting nanti.
3.  **Siapkan File Proyek (.zip):**
    *   Untuk Shared Hosting biasa, paling mudah adalah melakukan *zip* seluruh folder proyek Laravel Anda.
    *   **PENTING:** Anda BISA menyertakan folder `vendor`, agar tidak perlu repot menjalankan `composer install` di hosting jika hostingnya tidak mendukung SSH (meskipun Hostinger mendukungnya).
    *   **JANGAN** menyertakan folder `node_modules` (ukurannya sangat besar dan tidak dipakai di server *production*, karena kita sudah punya *build* dari Vite di folder `public/build`).
    *   Jadikan file `.zip` (misal `project-warehouse.zip`).

---

## 4. Langkah-Langkah Hosting (Contoh menggunakan Hostinger / hPanel)

Berikut adalah *step-by-step* mengunggah aplikasi Laravel ke Hostinger:

### Tahap 1: Setup Domain & Database
1.  Beli hosting dan domain di Hostinger.
2.  Masuk ke **hPanel** (Dashboard Hostinger).
3.  Pergi ke menu **Database** -> **Management Database (Manajemen Database)**.
4.  Buat Database baru:
    *   Nama Database: `u123456789_warehouse` (biasanya ada *prefix* unik)
    *   Username: `u123456789_admin`
    *   Password: `PasswordKuat123!`
    *   **Catat baik-baik ketiga info ini!**
5.  Masih di menu Database, klik tombol **Enter phpMyAdmin** untuk database yang baru dibuat.
6.  Di phpMyAdmin Hostinger, klik tab **Import**, pilih file `warehouse.sql` yang tadi di-export dari komputer Anda, lalu klik **Go**.

### Tahap 2: Upload File Proyek
1.  Di hPanel, pergi ke menu **Files** -> **File Manager** (Manajer File).
2.  Masuk ke direktori `public_html`.
3.  Upload file `project-warehouse.zip` ke dalam `public_html`.
4.  Klik kanan pada file `.zip` tersebut lalu pilih **Extract**. Ekstrak di dalam folder tersebut (misal menjadi folder `project-warehouse`).
5.  *(Opsional namun disarankan)* Pindahkan **seluruh isi** folder `project-warehouse` langsung ke dalam `public_html` (agar foldernya tidak bertumpuk).

### Tahap 3: Konfigurasi Laravel (.env)
1.  Di File Manager Hostinger (di dalam `public_html`), cari file `.env` (pastikan pengaturan "Show Hidden Files" aktif agar file berawalan titik terlihat).
2.  Klik kanan dan **Edit** file `.env`. Ubah konfigurasi berikut:

    ```env
    APP_NAME="WMS IndoOne"
    APP_ENV=production        <-- UBAH INI (awalnya local)
    APP_KEY=base64:...        <-- BIARKAN SAJA
    APP_DEBUG=false           <-- UBAH INI (agar error code tidak bocor ke user saat sidang)
    APP_URL=https://namadomainanda.com  <-- UBAH SESUAI DOMAIN ANDA

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1         <-- BIASANYA TETAP 127.0.0.1 DI HOSTING
    DB_PORT=3306
    DB_DATABASE=u123456789_warehouse  <-- ISI DENGAN NAMA DB HOSTING
    DB_USERNAME=u123456789_admin      <-- ISI DENGAN USERNAME DB HOSTING
    DB_PASSWORD=PasswordKuat123!      <-- ISI DENGAN PASSWORD DB HOSTING
    ```
3.  Simpan perubahan file `.env`.

### Tahap 4: Mengatur Document Root (Sangat Penting untuk Laravel)
Aplikasi Laravel menyimpan file yang boleh diakses publik di folder `public`. Jika Anda tidak mengatur ini, domain Anda (misal `domain.com`) akan menampilkan isi folder Laravel Anda, dan orang harus mengetik `domain.com/public` untuk masuk.

Di Hostinger (hPanel) ini sangat mudah:
1.  Cari atau ketik di kotak pencarian hPanel: **Domain** atau **Websites**.
2.  Pilih domain Anda, lalu masuk ke pengaturan (Manage).
3.  Cari opsi pengaturan PHP atau Direktori. Di hPanel Hostinger modern, ini biasanya ada di bagian **Advanced** -> **Folder Index** atau pengaturan **Document Root** pada menu **Websites**.
4.  Ubah **Document Root** dari `public_html` menjadi `public_html/public`.
5.  Simpan pengaturan.

### Tahap 5: Optimasi Akhir (Bisa via Terminal/SSH Hosting)
Di Hostinger paket Premium/Bisnis, Anda diberikan akses SSH / Terminal langsung di browser.
1. Buka menu **Advanced** -> **Terminal** di hPanel.
2. Masuk ke direktori web Anda: `cd public_html`
3. Jalankan perintah optimasi Laravel agar lebih cepat:
   ```bash
   php artisan optimize
   php artisan view:cache
   ```
4. Jalankan perintah *storage link* agar gambar/file yang diupload muncul (sangat penting untuk fitur ubah foto/file):
   ```bash
   php artisan storage:link
   ```

---

## 5. Cek Terakhir!
Buka domain Anda di *browser* (`https://namadomainanda.com`). Seharusnya halaman Login Sistem Manajemen Gudang akan langsung muncul. Coba login dengan akun admin atau petugas yang sudah ada di database.

Semoga sukses untuk skripsinya!
