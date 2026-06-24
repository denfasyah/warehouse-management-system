@extends('layouts.app')
@section('title', 'Dashboard Admin - Sistem Manajemen Gudang')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    {{-- Dashboard Active --}}
    <a class="flex items-center gap-2.5 px-3 py-2 bg-white/10 text-white rounded-lg font-semibold text-sm transition-all duration-200" href="#">
        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
        <span>Dashboard</span>
    </a>

    <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest opacity-40">Manajemen Utama</div>

    <a class="flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group" href="#">
        <span class="material-symbols-outlined text-[18px]">database</span>
        <span class="flex-1">Master Data</span>
        <span class="material-symbols-outlined text-[14px] opacity-40 group-hover:opacity-80 transition-opacity">chevron_right</span>
    </a>
    <a class="flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group" href="#">
        <span class="material-symbols-outlined text-[18px]">grid_view</span>
        <span class="flex-1">Class-Based Storage</span>
        <span class="material-symbols-outlined text-[14px] opacity-40 group-hover:opacity-80 transition-opacity">chevron_right</span>
    </a>
    <a class="flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all" href="#">
        <span class="material-symbols-outlined text-[18px]">analytics</span>
        <span>Laporan</span>
    </a>

    <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-widest opacity-40">Personal</div>

    <a class="flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all" href="#">
        <span class="material-symbols-outlined text-[18px]">person</span>
        <span>Profil</span>
    </a>
    <a class="flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-red-400 rounded-lg text-sm transition-all" href="#">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        <span>Logout</span>
    </a>
@endsection

@section('content')
{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Dashboard Ringkasan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Selamat datang kembali, pantau status inventaris hari ini.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <button class="bg-white text-gray-700 border border-gray-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5 text-sm font-medium hover:bg-gray-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
            Hari Ini
        </button>
        <button class="bg-primary text-white px-3 py-1.5 rounded-lg flex items-center gap-1.5 text-sm font-semibold hover:shadow-md hover:-translate-y-px transition-all">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Barang Baru
        </button>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    {{-- Stat Card 1 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-primary/30 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-primary/10 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings: 'FILL' 1;">package</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Barang</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">12.482</div>
            <div class="flex items-center gap-1 mt-0.5 text-green-600 text-[11px] font-semibold">
                <span class="material-symbols-outlined text-[13px]">trending_up</span>
                <span>+4.2%</span>
            </div>
        </div>
    </div>

    {{-- Stat Card 2 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-yellow-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-yellow-100 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-yellow-600" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fast Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">245</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Item aktif/minggu</p>
        </div>
    </div>

    {{-- Stat Card 3 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-gray-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-gray-100 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-gray-600">speed</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Medium</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">892</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Stabilitas menengah</p>
        </div>
    </div>

    {{-- Stat Card 4 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-red-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-red-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-red-500" style="font-variation-settings: 'FILL' 1;">hourglass_bottom</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Slow Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">56</div>
            <p class="text-[11px] text-red-500 font-bold mt-0.5">Butuh Perhatian</p>
        </div>
    </div>

    {{-- Stat Card 5 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-blue-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-blue-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-blue-600">move_to_inbox</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Masuk Hari Ini</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">1.2k</div>
        </div>
    </div>

    {{-- Stat Card 6 --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 group hover:border-orange-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-orange-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-orange-600">outbox</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Keluar Hari Ini</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">942</div>
        </div>
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Chart Placeholder --}}
    <div class="lg:col-span-2 glass-card p-5 rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Alur Barang Masuk vs Keluar</h3>
                <p class="text-xs text-gray-400 mt-0.5">Statistik operasional 7 hari terakhir.</p>
            </div>
            <div class="flex gap-2 text-[11px]">
                <span class="flex items-center gap-1 text-primary"><span class="inline-block w-2 h-2 rounded-full bg-primary"></span> Masuk</span>
                <span class="flex items-center gap-1 text-orange-500"><span class="inline-block w-2 h-2 rounded-full bg-orange-400"></span> Keluar</span>
            </div>
        </div>
        <div class="h-52 flex items-center justify-center border border-dashed border-gray-200 rounded-lg bg-gray-50">
            <p class="text-sm text-gray-400">Area Grafik Data Realtime</p>
        </div>
    </div>

    {{-- Storage Status --}}
    <div class="glass-card p-5 rounded-xl flex flex-col gap-4">
        <h3 class="text-base font-semibold text-gray-800">Penyimpanan Terpopuler</h3>
        <div class="space-y-3 flex-1">
            @foreach ([['Area A - Elektronik', 85, 'bg-primary'], ['Area B - Mekanik', 62, 'bg-yellow-400'], ['Area C - Kimia', 45, 'bg-green-500'], ['Area D - Umum', 30, 'bg-gray-400']] as [$name, $pct, $color])
            <div>
                <div class="flex justify-between mb-1">
                    <p class="text-xs font-medium text-gray-700">{{ $name }}</p>
                    <p class="text-xs text-gray-400">{{ $pct }}%</p>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="w-full py-2 rounded-lg bg-gray-50 border border-gray-200 text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all">Lihat Detail Mapping</button>
    </div>
</div>
@endsection
