@extends('layouts.app')
@section('title', 'Laporan Stok - Admin Panel')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Stok Barang</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau ketersediaan stok seluruh barang di gudang.</p>
    </div>
    <a href="{{ route('admin.reports.stock.pdf', request()->all()) }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
        Export PDF
    </a>
</div>

<div class="glass-card p-5 rounded-xl mb-6">
    <form action="{{ route('admin.reports.stock') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
            <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 w-full flex items-center h-[38px] mt-auto">
            <label class="flex items-center gap-2 cursor-pointer bg-white/50 px-3 py-2 rounded-lg border border-gray-200 hover:bg-white transition-colors w-full">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm text-gray-700">Hanya Stok Rendah (<= Minimum)</span>
            </label>
        </div>
        <div>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors h-[38px] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
        </div>
    </form>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-sm border border-white/40">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-600">
                <tr>
                    <th class="py-3 px-4 font-semibold">SKU / Nama Barang</th>
                    <th class="py-3 px-4 font-semibold">Kategori</th>
                    <th class="py-3 px-4 font-semibold">Stok Saat Ini</th>
                    <th class="py-3 px-4 font-semibold">Min. Stok</th>
                    <th class="py-3 px-4 font-semibold">Satuan</th>
                    <th class="py-3 px-4 font-semibold">Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white/30">
                @forelse($items as $item)
                <tr class="hover:bg-white/60 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-bold text-gray-800">{{ $item->sku }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $item->name }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                            {{ $item->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold {{ $item->stock <= $item->min_stock ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200' }}">
                            {{ number_format($item->stock) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600 font-medium">{{ number_format($item->min_stock) }}</td>
                    <td class="py-3 px-4 text-gray-600 uppercase text-xs font-bold">{{ $item->unit }}</td>
                    <td class="py-3 px-4">
                        @if($item->locations->first())
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                <span class="material-symbols-outlined text-[14px]">shelves</span>
                                {{ $item->locations->first()->code }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum ada</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl text-gray-400">inventory_2</span>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data stok yang ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
