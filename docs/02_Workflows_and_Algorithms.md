# Workflow Gudang

## Workflow Barang Masuk
1. Petugas melakukan **Login**.
2. Petugas mengklik menu **Input Barang Masuk**.
3. Sistem melakukan **Validasi Barang** (mengecek apakah data barang sudah terdaftar atau belum).
4. Jika valid, stok barang bertambah.
5. Sistem secara otomatis membuat **Barcode** untuk kuantitas tersebut (atau per tipe barang).
6. Barcode dicetak oleh Admin/Petugas, kemudian Petugas menempelkan Barcode di fisik barang.
7. Petugas menempatkan barang ke rak yang sesuai dengan rekomendasi sistem berdasarkan kelas storage.
8. Selesai.

## Workflow Barang Keluar
1. Petugas melakukan **Login**.
2. Petugas membuka menu **Scan Barcode** via HP.
3. Petugas mengarahkan kamera HP ke barcode barang. Sistem menampilkan detail barang.
4. Petugas menginput **Jumlah Keluar**.
5. Petugas mengirimkan **Permintaan Pengeluaran**. Status permintaan menjadi *Pending*.
6. Admin menerima notifikasi dan melakukan verifikasi.
7. Admin menekan tombol **Setujui (Approve)**.
8. Stok barang di database berkurang, transaksi tersimpan sebagai riwayat.
9. Selesai.

# Barcode Workflow
- Format Barcode: **Code128** (Dipilih karena efisiensi karakter alfanumerik dan kompatibilitas scanner yang luas).
- **Generate**: Dilakukan otomatis via *Observer* atau *Controller* saat barang baru didaftarkan atau barang masuk. String barcode bisa berupa kombinasi Kode Kategori dan Random Number unik (ex: `ELK-294819`).
- **Scanning**: Menggunakan library frontend JavaScript (HTML5-QRCode) yang merender input video dari kamera perangkat ke elemen HTML. Setelah berhasil dibaca, string akan dikirim ke backend via API/AJAX untuk meretrieve detail barang.

# Approval Workflow
- Permintaan pengeluaran barang masuk ke tabel `outgoing_goods` dengan status `pending`.
- Tabel memiliki kolom `status_approval` (enum: pending, approved, rejected) dan `approved_by` (nullable, diisi oleh ID Admin yang menyetujui).
- Ketika status diubah menjadi `approved`, event Laravel memicu pengurangan stok di tabel `items` menggunakan `DB::transaction`.

# Class-Based Storage Algorithm
Metode untuk menentukan letak tata ruang gudang berdasarkan tingkat kecepatan pergerakan barang.
**Pemicu:** Dijalankan secara berkala (contoh: *Cron Job* pada tanggal 1 setiap bulan).

**Formula & Threshold:**
1. Hitung total kuantitas barang yang keluar (Frekuensi Barang Keluar) dalam 30 hari terakhir.
2. Hitung persentase pengeluaran tiap barang dibandingkan total pengeluaran seluruh barang.
3. Kelompokkan dengan aturan ABC (Pareto):
   - **Fast Moving (A):** Barang yang menyumbang 70% dari total pengeluaran kumulatif.
   - **Medium Moving (B):** Barang yang menyumbang 20% berikutnya (71% - 90%).
   - **Slow Moving (C):** Barang yang menyumbang 10% terakhir (91% - 100%).

**Mapping Lokasi:**
Sistem memperbarui kolom `storage_class` di tabel `items`.
- Fast Moving $\rightarrow$ Rak Zona A (paling dekat dengan pintu keluar).
- Medium Moving $\rightarrow$ Rak Zona B (tengah).
- Slow Moving $\rightarrow$ Rak Zona C (paling dalam).
