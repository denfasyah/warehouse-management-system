# Deskripsi Sequence Diagram — Warehouse Management System

---

## 1. Lihat Riwayat Aktivitas Petugas

Diagram ini menggambarkan alur proses ketika seorang **Petugas** membuka halaman riwayat aktivitasnya melalui menu yang tersedia di sistem. Ketika halaman diakses, `ActivityController` menerima request melalui method `index()` lalu secara berurutan mengambil data penerimaan barang dari model `IncomingGood` (dengan query `WHERE user_id = auth()->id()`) dan data pengeluaran barang dari model `OutgoingGood` (dengan query `WHERE requested_by = auth()->id()`), keduanya diambil dari tabel masing-masing di basis data. Seluruh data kemudian digabungkan menggunakan `concat()`, diurutkan dari yang terbaru menggunakan `sortByDesc('created_at')`, dan dipaginasi sebanyak 15 data per halaman menggunakan `LengthAwarePaginator` sebelum dikirimkan ke view `petugas.activities.index`. Sebagai skenario alternatif, petugas juga dapat menerapkan filter berdasarkan tipe transaksi (`type=in` atau `type=out`) maupun rentang tanggal (`date_from` dan `date_to`), yang akan menyebabkan controller memfilter query ke basis data secara dinamis sebelum mengembalikan data yang sudah terfilter ke tampilan.

---

## 2. Lihat Laporan Admin

Diagram ini menggambarkan alur proses ketika seorang **Admin** mengakses halaman laporan stok barang yang dikelola oleh `ReportController`. Saat Admin membuka halaman laporan stok, controller memanggil `stockReport(request)` yang kemudian menjalankan query `Item::with(['category', 'locations'])->get()` untuk mengambil seluruh data barang beserta kategori dan lokasinya dari basis data, diikuti oleh pengambilan semua kategori melalui `Category::all()` sebagai data filter. Semua data tersebut dikembalikan ke view `admin.reports.stock` dan ditampilkan dalam bentuk tabel yang dapat difilter berdasarkan kategori maupun status stok rendah (`stock <= min_stock`). Sebagai skenario alternatif, Admin dapat menekan tombol **Export PDF** yang akan memanggil method `exportStockPdf()`, di mana controller mengambil kembali data barang sesuai filter yang aktif, kemudian menggunakan library **DomPDF** melalui `Pdf::loadView('stock-pdf')->download()` untuk menghasilkan dan mengunduh file `laporan-stok.pdf` secara langsung ke browser Admin.

---

## 3. Rekalkulasi CBS (Class-Based Storage)

Diagram ini menggambarkan alur proses ketika seorang **Admin** menjalankan fitur rekalkulasi Class-Based Storage (CBS) yang berfungsi menentukan ulang kelas penyimpanan (A, B, atau C) pada setiap barang berdasarkan frekuensi penggunaannya. Ketika Admin menekan tombol **Rekalkulasi CBS**, `CBSController` menerima request POST pada method `recalculate()` lalu menjalankan perintah Artisan `cbs:calculate` melalui `Artisan::call('cbs:calculate')`, di mana command tersebut mengambil seluruh data item beserta riwayat transaksi barang masuk dan keluar dari basis data, menghitung frekuensi pemakaian masing-masing item, menentukan kelasnya, lalu memperbarui kolom `storage_class` di tabel `items`. Setelah selesai, Admin diarahkan kembali ke halaman klasifikasi dengan notifikasi sukses. Sebagai skenario alternatif, Admin dapat menekan tombol **Generate Tugas Relokasi** yang memanggil `generateRelocationTasks()`; controller kemudian memfilter item yang lokasinya tidak sesuai kelasnya (`is_location_mismatch`), dan di dalam sebuah `DB::transaction()` membuat entri-entri `RelocationTask` ke basis data yang nantinya akan muncul sebagai tugas bagi Petugas untuk memindahkan barang secara fisik ke lokasi yang benar.

---

## 4. Kelola Barcode Admin

Diagram ini menggambarkan alur proses ketika seorang **Admin** melihat daftar barang dan mencetak barcode untuk suatu item tertentu melalui fitur yang disediakan sistem. Alur dimulai saat Admin membuka halaman daftar barang, di mana `ItemController` menjalankan `Item::with(['category', 'locations'])->paginate(15)` untuk mengambil data barang dari basis data dan menampilkannya dalam bentuk tabel yang dilengkapi tombol **Barcode** pada setiap baris. Ketika Admin mengklik tombol Barcode pada item tertentu, request diarahkan ke `BarcodeController` melalui route `GET /admin/items/{item}/barcode`; controller mengambil data item melalui route model binding, lalu menggunakan library **milon/barcode** dengan memanggil `DNS1D::getBarcodeHTML($item->sku, 'C128', 2, 60, 'black')` untuk membangkitkan kode barcode format Code 128 dalam bentuk HTML SVG, yang kemudian dirender pada view `admin.items.print-barcode`. Sebagai skenario alternatif, Admin dapat langsung mencetak barcode fisik dengan menekan tombol **Cetak** yang memicu `window.print()` di browser sehingga dialog cetak sistem operasi terbuka dan barcode dapat dicetak ke kertas label.
