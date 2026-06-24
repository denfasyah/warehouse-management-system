# Project Overview
Sistem Manajemen Gudang (Warehouse Management System) ini dirancang untuk mendigitalisasi dan mengotomatisasi pencatatan stok barang, pengaturan lokasi penyimpanan, serta proses keluar-masuk barang. Sistem ini mengintegrasikan **Metode Class-Based Storage** untuk optimasi tata letak barang berdasarkan kecepatan perputaran (Fast, Medium, Slow moving) dan **Teknologi Barcode (Code128)** untuk akurasi dan kecepatan pelacakan fisik barang.

# Business Rules
1. **Identifikasi Barang**: Setiap barang yang masuk ke gudang wajib memiliki Barcode unik yang digenerate oleh sistem.
2. **Penyimpanan**: Barang harus ditempatkan pada rak yang sesuai dengan kelas penyimpanannya (Fast, Medium, Slow). Sistem akan memberikan rekomendasi rak.
3. **Validasi Keluar**: Pengeluaran barang tidak mengurangi stok secara langsung hingga mendapatkan status persetujuan (*Approved*) dari Admin.
4. **Stok Minimum/Maksimum**: Barang tidak bisa dikeluarkan melebihi stok yang tersedia.
5. **Pembaruan Kelas Storage**: Kelas storage diperbarui setiap akhir bulan berdasarkan kalkulasi frekuensi pengeluaran.

# Role Permission Matrix

| Fitur / Modul | Admin Gudang | Petugas Gudang |
| :--- | :---: | :---: |
| Login & Logout | ✅ | ✅ |
| Kelola User | ✅ | ❌ |
| Kelola Master Data (Barang, Kategori) | ✅ | ❌ |
| Generate & Cetak Barcode | ✅ | ❌ |
| Kelola Lokasi Penyimpanan & Threshold | ✅ | ❌ |
| Approval Barang Keluar | ✅ | ❌ |
| Lihat & Cetak Laporan (PDF) | ✅ | ❌ |
| Input Barang Masuk | ❌ | ✅ |
| Input Permintaan Barang Keluar | ❌ | ✅ |
| Scan Barcode (Mobile) | ❌ | ✅ |
| Penataan Barang & Lihat Lokasi | ❌ | ✅ |
| Lihat Status Approval | ❌ | ✅ |
