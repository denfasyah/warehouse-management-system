# Panduan Step-by-Step Hosting Proyek Laravel di Hostinger Premium

Panduan ini berisi langkah-langkah lengkap untuk meng-hosting proyek WMS (Laravel 10) Anda ke **Hostinger (Paket Premium Web Hosting)**. Panduan ini juga membagi peran antara **User (Anda)** dan **Agent (Saya)** agar proses hosting menjadi lebih terstruktur dan efisien.

---

## Pembagian Peran

### 🤖 Apa yang bisa dikerjakan oleh Agent (Saya):
1. **Optimasi & Build File Lokal:** Menjalankan perintah build (seperti `npm run build`) untuk menyiapkan file CSS/JS versi *production*.
2. **Export Database:** Mengekspor struktur dan data dari database lokal Anda menjadi file `.sql`.
3. **Pembuatan File Arsip (.zip):** Membungkus (zip) seluruh proyek Laravel secara otomatis (dengan mengecualikan folder berat yang tidak perlu seperti `node_modules` atau `.git`).
4. **Persiapan Script Otomasi:** Membuatkan script (contoh: route khusus) untuk menjalankan perintah artisan (seperti *storage:link*, *cache:clear*) jika Anda kesulitan menggunakan SSH di Hostinger.
5. **Penyesuaian Konfigurasi:** Menyiapkan teks/kode untuk file `.env` versi *production* yang nantinya tinggal Anda *copy-paste*.

### 👤 Apa yang harus dikerjakan oleh User (Anda):
1. **Akses Dashboard:** Login ke akun Hostinger (hPanel) Anda.
2. **Manajemen Database:** Membuat Database, User Database, dan Password baru di hPanel, lalu mengimpor file `.sql`.
3. **Upload File:** Mengunggah file `.zip` ke File Manager Hostinger dan mengekstraknya.
4. **Konfigurasi Document Root:** Mengubah pengaturan *Document Root* (folder utama) website agar mengarah ke folder `public` Laravel.
5. **Edit .env:** Mengubah konfigurasi koneksi database di dalam file `.env` di server hosting.

---

## 🚀 Step-by-Step Proses Hosting

### TAHAP 1: Persiapan di Komputer Lokal (Bisa dibantu Agent)

1. **Build Assets Frontend**
   - Jalankan perintah `npm run build` untuk memproses *TailwindCSS/Vite* menjadi file statis siap rilis. *(Beri tahu saya jika ingin saya jalankan)*
2. **Export Database (Backup)**
   - Export database lokal `warehouse` menjadi file `warehouse.sql`. *(Beri tahu saya jika ingin saya export-kan, atau Anda bisa melakukannya via localhost/phpmyadmin)*.
3. **Zipping Project**
   - Kompres seluruh isi folder proyek (Laravel) menjadi file `.zip` (misal: `wms-app.zip`). 
   - **PENTING:** Pastikan folder `vendor` ikut di-zip, tapi folder `node_modules` **jangan** dimasukkan karena ukurannya sangat besar dan tidak dipakai di server *production*. *(Beri tahu saya jika ingin saya buatkan zip-nya secara otomatis)*.

### TAHAP 2: Konfigurasi di Hostinger hPanel (Oleh User)

1. **Buat Database MySQL**
   - Login ke **Hostinger hPanel**.
   - Pilih menu **Databases** -> **Management**.
   - Buat database baru (misal nama database, username, dan password). **Catat kredensial ini baik-baik!**
2. **Import Database**
   - Di menu Databases yang sama, klik tombol **Enter phpMyAdmin** di sebelah database yang baru Anda buat.
   - Pilih tab **Import**, pilih file `warehouse.sql` dari komputer Anda, lalu klik **Go**.
3. **Upload File Proyek**
   - Kembali ke hPanel, buka menu **Files** -> **File Manager**.
   - Masuk ke folder website Anda (biasanya `domains/namadomain.com/public_html`).
   - Hapus file bawaan Hostinger (seperti `default.php`).
   - Upload file `wms-app.zip` yang dibuat di Tahap 1.
   - Klik kanan pada file zip tersebut lalu pilih **Extract**. Pastikan hasil ekstrakannya (isi folder Laravel seperti `app`, `bootstrap`, `public`, dll) berada persis di dalam folder `public_html`, bukan di dalam sub-folder lagi.

### TAHAP 3: Konfigurasi Website di Hostinger (Oleh User & Agent)

1. **Edit File `.env` (User)**
   - Di File Manager Hostinger, cari file `.env` (jika tidak terlihat, pastikan fitur *Show Hidden Files* aktif di File Manager).
   - Klik ganda untuk mengedit. Ubah bagian berikut:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://namadomainanda.com

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=u123456789_warehouse (Sesuaikan dengan nama database di Hostinger)
     DB_USERNAME=u123456789_user (Sesuaikan dengan username database di Hostinger)
     DB_PASSWORD=PasswordDatabaseAnda!
     ```
   - Klik **Save**.
2. **Atur Document Root (Sangat Penting! - User)**
   - Laravel membutuhkan folder `public` sebagai pintu masuk utama demi keamanan.
   - Di hPanel, buka menu **Advanced** -> **PHP Configuration** (atau di pengaturan Domain).
   - Cari opsi **Document Root** atau **Web Directory**.
   - Ubah jalurnya dari `public_html` menjadi `public_html/public`.
   - Simpan perubahan. (Note: Paket Premium Hostinger hPanel umumnya mendukung perubahan Document Root. Jika tidak menemukan fiturnya, beri tahu saya agar saya bantu buatkan file `.htaccess` alternatif).

### TAHAP 4: Finalisasi (Bisa dibantu Agent)

Agar aplikasi Laravel (terutama fitur penyimpanan file, foto, dll) berfungsi sempurna di server, folder storage harus di-link.
Jika Anda punya akses SSH (Paket Premium Hostinger menyediakannya):
- Buka terminal/SSH.
- Masuk ke folder web: `cd domains/namadomain.com/public_html`.
- Jalankan `php artisan storage:link` dan `php artisan optimize:clear`.

*(Jika Anda tidak mengerti cara menggunakan SSH, beri tahu saya. Saya bisa membuatkan sebuah file PHP sederhana sementara (misal `setup.php`) yang jika Anda buka di browser, ia akan otomatis menjalankan perintah di atas.)*

---

🎉 **SELESAI!** Sekarang coba buka nama domain Anda di browser. Proyek Warehouse Management System Anda seharusnya sudah bisa diakses.

**Apa langkah Anda selanjutnya?**
Katakan pada saya: *"Agent, tolong siapkan file zip proyeknya dan script export database-nya sekarang"* jika Anda ingin memulai Tahap 1!
