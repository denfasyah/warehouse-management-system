@extends('layouts.app')
@section('title', 'Dashboard Admin - Sistem Manajemen Gudang')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
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
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-primary/40 transition-all cursor-default group relative">
        @if($stats['low_stock_count'] > 0)
        <span class="absolute -top-1 -right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-primary/10 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings: 'FILL' 1;">package</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Barang</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['total_stock']) }}</div>
            <div class="flex items-center gap-1 mt-0.5 {{ $stats['low_stock_count'] > 0 ? 'text-red-500' : 'text-gray-400' }} text-[11px] font-semibold">
                @if($stats['low_stock_count'] > 0)
                <span class="material-symbols-outlined text-[13px]">warning</span> {{ $stats['low_stock_count'] }} item low stock
                @else
                <span class="material-symbols-outlined text-[13px]">check_circle</span> Stok aman
                @endif
            </div>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-red-400/50 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-red-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-red-600" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fast Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['fast'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Macam barang</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-yellow-400 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-yellow-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-yellow-600">speed</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Medium</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['medium'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Macam barang</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-green-300 transition-all cursor-default relative">
        @if($stats['mismatch_count'] > 0)
        <a href="{{ route('admin.cbs.classification') }}" title="{{ $stats['mismatch_count'] }} barang perlu relokasi" class="absolute top-2 right-2 flex items-center justify-center w-6 h-6 bg-orange-100 text-orange-600 rounded-full hover:bg-orange-200 transition-colors">
            <span class="material-symbols-outlined text-[14px]">warning</span>
        </a>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-green-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-green-500" style="font-variation-settings: 'FILL' 1;">hourglass_bottom</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Slow Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['slow'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">Macam barang</p>
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
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['incoming_today']) }}</div>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-orange-300 transition-all cursor-default relative">
        @if($stats['pending_count'] > 0)
        <a href="{{ route('admin.approvals.index') }}" title="{{ $stats['pending_count'] }} pengajuan menunggu" class="absolute top-2 right-2 flex items-center justify-center min-w-[24px] h-6 px-1.5 bg-orange-500 text-white text-[10px] font-bold rounded-full hover:bg-orange-600 transition-colors shadow-sm">
            {{ $stats['pending_count'] }}
        </a>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-orange-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-orange-500">outbox</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Keluar Hari Ini</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['outgoing_today']) }}</div>
        </div>
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Chart --}}
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
        <div class="h-52 w-full relative">
            <canvas id="flowChart"></canvas>
        </div>
    </div>

    {{-- Storage Status --}}
    <div class="glass-card p-5 rounded-xl flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Kapasitas Penyimpanan</h3>
            <a href="{{ route('admin.cbs.mapping') }}" class="text-xs text-primary hover:underline">Detail →</a>
        </div>
        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
            @forelse ($stats['zone_stats'] as $zone)
            @php
                $pct = $zone->total_capacity > 0 ? round(($zone->total_fill / $zone->total_capacity) * 100) : 0;
                $color = $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-yellow-500' : 'bg-green-500');
                $textColor = $pct > 80 ? 'text-red-500' : 'text-gray-500';
            @endphp
            <div>
                <div class="flex justify-between mb-1">
                    <p class="text-xs font-medium text-gray-700">Zona {{ $zone->zone }}</p>
                    <p class="text-xs font-semibold {{ $textColor }}">{{ number_format($zone->total_fill) }} / {{ number_format($zone->total_capacity) }} ({{ $pct }}%)</p>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="{{ $color }} h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-xs text-gray-400">Belum ada data lokasi rak</div>
            @endforelse
        </div>
        <a href="{{ route('admin.cbs.mapping') }}" class="w-full py-2 rounded-lg bg-gray-50 border border-gray-200 text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all text-center block">Lihat Mapping Storage</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('flowChart').getContext('2d');
        const chartData = @json($stats['chart_days']);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [
                    {
                        label: 'Masuk',
                        data: chartData.map(d => d.incoming),
                        borderColor: '#4F46E5', // primary color
                        backgroundColor: '#4F46E520',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Keluar',
                        data: chartData.map(d => d.outgoing),
                        borderColor: '#F97316', // orange color
                        backgroundColor: '#F9731620',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
