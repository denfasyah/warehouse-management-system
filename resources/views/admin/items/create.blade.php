@extends('layouts.app')
@section('title', 'Tambah Data Barang - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.items.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors text-gray-600">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Tambah Barang Baru</h2>
        <p class="text-sm text-gray-500 mt-0.5">Input master data barang ke dalam sistem gudang.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">
    <form action="{{ route('admin.items.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Kolom Kiri -->
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20" placeholder="Contoh: Lampu LED Philips 10W">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">SKU / Kode <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="text" name="sku" value="{{ old('sku') }}" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 font-mono text-sm uppercase" placeholder="Auto Generate">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Rak Penyimpanan <span class="text-red-500">*</span></label>
                    <select name="location_ids[]" multiple required class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20" size="4">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (is_array(old('location_ids')) && in_array($loc->id, old('location_ids'))) ? 'selected' : '' }}>
                                {{ $loc->code }} (Kelas: {{ ucfirst($loc->storage_class) }} | Sisa: {{ $loc->capacity - $loc->current_fill }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1">Tahan tombol Ctrl/Cmd untuk memilih lebih dari satu lokasi.</p>
                    @error('location_ids') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Stok Awal dan Satuan dihapus (diinput oleh petugas saat barang masuk) -->
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Stok (Threshold) <span class="text-red-500">*</span></label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" required min="0" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                    <p class="text-[11px] text-gray-500 mt-1">Sistem akan memberi peringatan jika stok di bawah angka ini.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Barang</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring focus:ring-primary/20" placeholder="Detail spesifikasi barang (opsional)">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
            <button type="reset" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Form</button>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-xl transition-colors shadow-sm">Simpan Barang</button>
        </div>
    </form>
</div>
@endsection
