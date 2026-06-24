@extends('layouts.app')
@section('title', 'Data Barang - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Data Barang (Master)</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola seluruh data inventaris di dalam sistem pergudangan.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.items.export') }}" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">export_notes</span>
            Export CSV
        </a>
        <a href="{{ route('admin.items.create') }}" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 shadow-sm transition-all active:scale-95">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Barang
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
    <form action="{{ route('admin.items.index') }}" method="GET" class="flex-1 flex gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400">search</span>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau SKU..." class="pl-10 w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
        </div>
        <div class="w-48">
            <select name="category" class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary/10 text-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-primary/20 transition-colors">
            Filter
        </button>
        @if(request()->has('search') || request()->has('category'))
            <a href="{{ route('admin.items.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-colors">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Card Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Item & Kategori</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">SKU / Barcode</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Lokasi / Rak</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Stok</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $item->name }}</p>
                        <p class="text-xs text-primary font-medium mt-0.5">{{ $item->category->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-mono font-bold tracking-widest">{{ $item->sku }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-surface-container-high text-on-surface rounded-md text-xs font-semibold">{{ $item->location->code }}</span>
                            @if($item->class_mismatch)
                                <span title="Mismatch CBS (Barang Fast Moving di Rak Slow Moving dsb)" class="material-symbols-outlined text-orange-500 text-[18px] cursor-help">warning</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-lg font-black {{ $item->is_low_stock ? 'text-red-600' : 'text-gray-800' }} leading-none">
                                {{ $item->stock }}
                            </span>
                            <span class="text-[10px] text-gray-500 uppercase mt-1">{{ $item->unit }}</span>
                            
                            @if($item->is_low_stock)
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded mt-1 border border-red-200">Stok Menipis</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-1">
                        <a href="{{ route('admin.barcode.print', $item->slug) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors" title="Cetak Barcode">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                        </a>
                        <a href="{{ route('admin.items.edit', $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Edit Barang">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <form action="{{ route('admin.items.destroy', $item->slug) }}" method="POST" class="inline-block" id="delete-form-{{ $item->id }}">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="button" 
                                onclick="confirmDelete('delete-form-{{ $item->id }}')" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus Barang"
                            >
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">inventory_2</span>
                            <p>Data barang belum tersedia atau tidak ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($items->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $items->links() }}
    </div>
    @endif
</div>

<script>
function confirmDelete(formId) {
    Swal.fire({
        title: 'Hapus Data Barang?',
        text: "Data yang sudah dihapus tidak bisa dikembalikan. Barang dengan sisa stok tidak dapat dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba1a1a',
        cancelButtonColor: '#e0e3e5',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: '<span class="text-gray-700">Batal</span>',
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-gray-100',
            title: 'font-headline-md text-gray-800',
            confirmButton: 'rounded-lg px-6',
            cancelButton: 'rounded-lg px-6'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    })
}
</script>
@endsection
