@extends('layouts.app')
@section('title', 'Dashboard Petugas - Sistem Manajemen Gudang')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
{{-- === DASHBOARD === --}}
<a href="#" class="flex items-center gap-2.5 px-3 py-2 bg-white/10 text-white rounded-lg font-semibold text-sm">
    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
    <span>Dashboard</span>
</a>

{{-- === TRANSAKSI === --}}
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all">
        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
        <span class="flex-1 text-left">Transaksi</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">move_to_inbox</span> Barang Masuk
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">outbox</span> Barang Keluar
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span> Scan Barcode
        </a>
    </div>
</div>

{{-- === STORAGE === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all">
        <span class="material-symbols-outlined text-[18px]">shelves</span>
        <span class="flex-1 text-left">Storage</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">location_on</span> Lokasi Penyimpanan
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">move_item</span> Penataan Barang
        </a>
    </div>
</div>

{{-- === RIWAYAT AKTIVITAS === --}}
<a href="#" class="mt-1 flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all">
    <span class="material-symbols-outlined text-[18px]">history</span>
    <span>Riwayat Aktivitas</span>
</a>
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Halo, Budi! 👋</h2>
        <p class="text-sm text-gray-500 mt-0.5">Sesi aktif di Gudang Sektor B-4 sedang berjalan.</p>
    </div>
    <div class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full flex items-center gap-1.5 text-xs font-semibold self-start sm:self-auto">
        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
        SISTEM ONLINE: 29/07/2024
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Barang Masuk Hari Ini</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-primary leading-none">124</span>
                <span class="text-xs text-gray-400 pb-0.5">Items</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default border-l-4 border-l-yellow-400">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Barang Keluar Hari Ini</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-yellow-600 leading-none">86</span>
                <span class="text-xs text-gray-400 pb-0.5">Items</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">outbox</span>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Total Scan Hari Ini</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-primary leading-none">210</span>
                <span class="text-xs text-gray-400 pb-0.5">Scans</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mb-5">
    <div class="flex items-center gap-1.5 mb-3">
        <span class="material-symbols-outlined text-[18px] text-primary" style="font-variation-settings: 'FILL' 1;">bolt</span>
        <h3 class="text-base font-semibold text-gray-800">Aksi Cepat</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <button class="bg-primary text-white p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:opacity-90 transition-all hover:shadow-md group text-center">
            <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">qr_code_scanner</span>
            <div>
                <div class="font-semibold text-sm">Scan Barcode</div>
                <div class="text-xs opacity-70 mt-0.5">Gunakan kamera/scanner</div>
            </div>
        </button>
        <button class="glass-card p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:border-primary/50 hover:shadow-sm transition-all group text-center">
            <span class="material-symbols-outlined text-3xl text-primary group-hover:-translate-y-0.5 transition-transform">playlist_add</span>
            <div>
                <div class="font-semibold text-sm text-gray-800">Barang Masuk</div>
                <div class="text-xs text-gray-400 mt-0.5">Penerimaan stok baru</div>
            </div>
        </button>
        <button class="glass-card p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:border-primary/50 hover:shadow-sm transition-all group text-center">
            <span class="material-symbols-outlined text-3xl text-primary group-hover:-translate-y-0.5 transition-transform">local_shipping</span>
            <div>
                <div class="font-semibold text-sm text-gray-800">Barang Keluar</div>
                <div class="text-xs text-gray-400 mt-0.5">Pengiriman & Distribusi</div>
            </div>
        </button>
    </div>
</div>

{{-- Activity Log --}}
<div class="glass-card rounded-xl overflow-hidden">
    <div class="px-5 py-3.5 flex items-center justify-between border-b border-gray-100">
        <h3 class="text-base font-semibold text-gray-800">Riwayat Aktivitas Terbaru</h3>
        <a href="#" class="text-primary text-sm font-semibold hover:underline">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[540px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                    <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aktivitas</th>
                    <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Detail Barang</th>
                    <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lokasi Rak</th>
                    <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ([
                    ['14:25', 'Barang Masuk', 'Box Panel Listrik 30x40', 'A-02-14', 'Berhasil', true],
                    ['13:50', 'Barang Keluar', 'Kabel NYY 4x10mm', 'C-10-01', 'Berhasil', true],
                    ['11:15', 'Scan Barcode', 'Isolasi Nitto Black', 'B-05-05', 'Pending', false],
                    ['09:40', 'Barang Masuk', 'Lampu LED 12W Philips', 'A-01-02', 'Berhasil', true],
                ] as [$time, $act, $item, $loc, $status, $ok])
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap font-mono">{{ $time }}</td>
                    <td class="px-5 py-3 text-sm text-gray-700 font-medium">{{ $act }}</td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $item }}</td>
                    <td class="px-5 py-3 text-sm font-mono text-gray-500">{{ $loc }}</td>
                    <td class="px-5 py-3">
                        @if($ok)
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wide">{{ $status }}</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-[10px] font-bold uppercase tracking-wide">{{ $status }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
