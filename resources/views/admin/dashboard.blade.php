@extends('layouts.app')
@section('title', 'Dashboard Admin - Sistem Manajemen Gudang')
@section('role', 'ADMIN PANEL')

@section('sidebar')
{{-- === DASHBOARD === --}}
<a href="#" class="flex items-center gap-2.5 px-3 py-2 bg-white/10 text-white rounded-lg font-semibold text-sm">
    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
    <span>Dashboard</span>
</a>

{{-- === MASTER DATA === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">database</span>
        <span class="flex-1 text-left">Master Data</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">inventory_2</span> Data Barang
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">category</span> Kategori Barang
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">qr_code_2</span> Generate & Cetak Barcode
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">shelves</span> Lokasi Penyimpanan
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">manage_accounts</span> Manajemen User
        </a>
    </div>
</div>

{{-- === CLASS-BASED STORAGE === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">grid_view</span>
        <span class="flex-1 text-left">Class-Based Storage</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">sort</span> Klasifikasi Barang
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">map</span> Mapping Storage
        </a>
    </div>
</div>

{{-- === PERSETUJUAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">approval</span>
        <span class="flex-1 text-left">Persetujuan</span>
        {{-- Badge notifikasi --}}
        <span class="ml-auto mr-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full leading-none">3</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">task_alt</span> Approval Barang Keluar
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">history</span> Riwayat Persetujuan
        </a>
    </div>
</div>

{{-- === LAPORAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">analytics</span>
        <span class="flex-1 text-left">Laporan</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">inventory</span> Laporan Stok
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">move_to_inbox</span> Lap. Barang Masuk
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">outbox</span> Lap. Barang Keluar
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">warehouse</span> Laporan Storage
        </a>
    </div>
</div>

{{-- === PENGATURAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">settings</span>
        <span class="flex-1 text-left">Pengaturan</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">tune</span> Threshold CBS
        </a>
    </div>
</div>
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
        <button class="bg-primary text-white px-3 py-1.5 rounded-lg flex items-center gap-1.5 text-sm font-semibold hover:opacity-90 hover:shadow-md transition-all">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Barang Baru
        </button>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-primary/40 transition-all cursor-default group">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-primary/10 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings: 'FILL' 1;">package</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Barang</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">12.482</div>
            <div class="flex items-center gap-1 mt-0.5 text-green-600 text-[11px] font-semibold">
                <span class="material-symbols-outlined text-[13px]">trending_up</span> +4.2%
            </div>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-yellow-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-yellow-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-yellow-600" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fast Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">245</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Item aktif/minggu</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-gray-300 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-gray-100 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-gray-500">speed</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Medium</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">892</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Stabilitas menengah</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-red-300 transition-all cursor-default">
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
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-blue-300 transition-all cursor-default">
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
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-orange-300 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-orange-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-orange-500">outbox</span>
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
            <div class="flex gap-3 text-[11px] text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary inline-block"></span> Masuk</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span> Keluar</span>
            </div>
        </div>
        <div class="h-52 flex items-center justify-center border border-dashed border-gray-200 rounded-lg bg-gray-50">
            <div class="text-center">
                <span class="material-symbols-outlined text-4xl text-gray-300">bar_chart</span>
                <p class="text-sm text-gray-400 mt-1">Grafik akan tampil setelah data terhubung</p>
            </div>
        </div>
    </div>

    {{-- Storage Status --}}
    <div class="glass-card p-5 rounded-xl flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Kapasitas Penyimpanan</h3>
            <a href="#" class="text-xs text-primary hover:underline">Detail →</a>
        </div>
        <div class="space-y-3 flex-1">
            @foreach ([['Area A - Fast Moving', 85, 'bg-red-400'], ['Area B - Medium Moving', 62, 'bg-yellow-400'], ['Area C - Slow Moving', 45, 'bg-green-500'], ['Area D - Umum', 30, 'bg-gray-300']] as [$name, $pct, $color])
            <div>
                <div class="flex justify-between mb-1">
                    <p class="text-xs font-medium text-gray-700">{{ $name }}</p>
                    <p class="text-xs font-semibold {{ $pct > 80 ? 'text-red-500' : 'text-gray-400' }}">{{ $pct }}%</p>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="{{ $color }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <a href="#" class="w-full py-2 rounded-lg bg-gray-50 border border-gray-200 text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all text-center block">Lihat Mapping Storage</a>
    </div>
</div>
@endsection
