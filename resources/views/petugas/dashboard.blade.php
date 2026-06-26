@extends('layouts.app')
@section('title', 'Dashboard Petugas - Sistem Manajemen Gudang')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Halo, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-sm text-gray-500 mt-0.5">Sesi aktif di Gudang Sektor B-4 sedang berjalan.</p>
    </div>
    <div class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full flex items-center gap-1.5 text-xs font-semibold self-start sm:self-auto">
        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
        SISTEM ONLINE: 29/07/2024
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default border-l-4 border-l-primary">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Barang Masuk Hari Ini</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-primary leading-none">{{ number_format($stats['incoming_today']) }}</span>
                <span class="text-xs text-gray-400 pb-0.5">Items</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default border-l-4 border-l-orange-400">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Barang Keluar Hari Ini</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-orange-600 leading-none">{{ number_format($stats['outgoing_today']) }}</span>
                <span class="text-xs text-gray-400 pb-0.5">Items</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">outbox</span>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex justify-between items-center group cursor-default border-l-4 border-l-blue-400">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Total Stok Gudang</p>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-bold text-blue-600 leading-none">{{ number_format($stats['total_stock']) }}</span>
                <span class="text-xs text-gray-400 pb-0.5">Items</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[18px]">inventory</span>
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
        <a href="{{ route('petugas.scanner.index') }}" class="bg-primary text-white p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:opacity-90 transition-all hover:shadow-md group text-center">
            <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">qr_code_scanner</span>
            <div>
                <div class="font-semibold text-sm">Scan Barcode</div>
                <div class="text-xs opacity-70 mt-0.5">Gunakan kamera/scanner</div>
            </div>
        </a>
        <a href="{{ route('petugas.incoming.create') }}" class="glass-card p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:border-primary/50 hover:shadow-sm transition-all group text-center">
            <span class="material-symbols-outlined text-3xl text-primary group-hover:-translate-y-0.5 transition-transform">playlist_add</span>
            <div>
                <div class="font-semibold text-sm text-gray-800">Barang Masuk</div>
                <div class="text-xs text-gray-400 mt-0.5">Penerimaan stok baru</div>
            </div>
        </a>
        <a href="{{ route('petugas.outgoing.create') }}" class="glass-card p-5 rounded-xl flex flex-col items-center justify-center gap-2.5 hover:border-primary/50 hover:shadow-sm transition-all group text-center">
            <span class="material-symbols-outlined text-3xl text-primary group-hover:-translate-y-0.5 transition-transform">local_shipping</span>
            <div>
                <div class="font-semibold text-sm text-gray-800">Barang Keluar</div>
                <div class="text-xs text-gray-400 mt-0.5">Pengiriman & Distribusi</div>
            </div>
        </a>
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
                @forelse ($activities as $act)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap font-mono">{{ $act['time'] }}</td>
                    <td class="px-5 py-3 text-sm text-gray-700 font-medium">{{ $act['act'] }}</td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $act['item'] }}</td>
                    <td class="px-5 py-3 text-sm font-mono text-gray-500">{{ $act['loc'] }}</td>
                    <td class="px-5 py-3">
                        @if($act['ok'])
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wide">{{ $act['status'] }}</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-[10px] font-bold uppercase tracking-wide">{{ $act['status'] }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm italic">Belum ada riwayat aktivitas hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
