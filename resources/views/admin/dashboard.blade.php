@extends('layouts.app')
@section('title', 'Dashboard Admin - Sistem Manajemen Gudang')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
{{-- ───────────────────────────────────────────────
     Page Header + Period Filter
────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Dashboard Ringkasan</h2>
        <p class="text-sm text-gray-500 mt-0.5">
            Menampilkan data untuk: <span class="font-semibold text-primary">{{ $stats['period_label'] }}</span>
        </p>
    </div>
    {{-- Period Filter --}}
    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl shrink-0">
        @foreach([1 => 'Hari Ini', 7 => '7 Hari', 30 => '30 Hari'] as $val => $label)
            <a href="{{ route('admin.dashboard', ['period' => $val]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                      {{ $period === $val ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ───────────────────────────────────────────────
     Stats Cards Row
────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    {{-- Total Stok --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-primary/40 transition-all cursor-default relative">
        @if($stats['low_stock_count'] > 0)
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-primary/10 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings:'FILL' 1;">package</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Total Stok</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['total_stock']) }}</div>
            <div class="flex items-center gap-1 mt-0.5 {{ $stats['low_stock_count'] > 0 ? 'text-red-500' : 'text-green-500' }} text-[11px] font-semibold">
                <span class="material-symbols-outlined text-[13px]">{{ $stats['low_stock_count'] > 0 ? 'warning' : 'check_circle' }}</span>
                {{ $stats['low_stock_count'] > 0 ? $stats['low_stock_count'].' item low stock' : 'Aman' }}
            </div>
        </div>
    </div>

    {{-- Fast Moving --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-red-300 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-red-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-red-600" style="font-variation-settings:'FILL' 1;">bolt</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fast Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['fast'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">macam barang</p>
        </div>
    </div>

    {{-- Medium Moving --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-yellow-300 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-yellow-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-yellow-600">speed</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Medium</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['medium'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">macam barang</p>
        </div>
    </div>

    {{-- Slow Moving --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-green-300 transition-all cursor-default relative">
        @if($stats['mismatch_count'] > 0)
            <a href="{{ route('admin.cbs.classification') }}"
               title="{{ $stats['mismatch_count'] }} barang perlu relokasi"
               class="absolute top-2 right-2 flex items-center justify-center w-6 h-6 bg-orange-100 text-orange-600 rounded-full hover:bg-orange-200 transition-colors">
                <span class="material-symbols-outlined text-[14px]">warning</span>
            </a>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-green-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-green-500" style="font-variation-settings:'FILL' 1;">hourglass_bottom</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Slow Moving</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['class_counts']['slow'] ?? 0) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">macam barang</p>
        </div>
    </div>

    {{-- Barang Masuk --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-blue-300 transition-all cursor-default">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-blue-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-blue-600">move_to_inbox</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Barang Masuk</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['incoming_today']) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $stats['period_label'] }}</p>
        </div>
    </div>

    {{-- Barang Keluar --}}
    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 hover:border-orange-300 transition-all cursor-default relative">
        @if($stats['pending_count'] > 0)
            <a href="{{ route('admin.approvals.index') }}"
               title="{{ $stats['pending_count'] }} pengajuan menunggu"
               class="absolute top-2 right-2 flex items-center justify-center min-w-[22px] h-[22px] px-1.5 bg-orange-500 text-white text-[10px] font-bold rounded-full hover:bg-orange-600 transition-colors shadow-sm">
                {{ $stats['pending_count'] }}
            </a>
        @endif
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-orange-50 rounded-lg">
                <span class="material-symbols-outlined text-[16px] text-orange-500">outbox</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-tight">Barang Keluar</span>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($stats['outgoing_today']) }}</div>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $stats['period_label'] }}</p>
        </div>
    </div>
</div>

{{-- ───────────────────────────────────────────────
     Charts Row
────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Chart 1: Masuk vs Keluar (combined) — 2/3 width --}}
    <div class="lg:col-span-2 glass-card p-5 rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Barang Masuk vs Keluar</h3>
                <p class="text-xs text-gray-400 mt-0.5">Perbandingan volume transaksi — {{ $stats['period_label'] }}</p>
            </div>
            <div class="flex gap-3 text-[11px] text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Masuk
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span> Keluar
                </span>
            </div>
        </div>
        <div class="h-48 w-full">
            <canvas id="flowChart"></canvas>
        </div>
    </div>

    {{-- Kapasitas Penyimpanan --}}
    <div class="glass-card p-5 rounded-xl flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-800">Kapasitas Penyimpanan</h3>
            <a href="{{ route('admin.cbs.mapping') }}" class="text-xs text-primary hover:underline font-medium">Detail →</a>
        </div>
        <div class="space-y-3 flex-1">
            @forelse ($stats['zone_stats'] as $zone)
                @php
                    $pct       = $zone->total_capacity > 0 ? round(($zone->total_fill / $zone->total_capacity) * 100) : 0;
                    $barColor  = $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-yellow-500' : 'bg-green-500');
                    $textColor = $pct > 80 ? 'text-red-600' : 'text-gray-500';
                @endphp
                <div>
                    <div class="flex justify-between mb-1">
                        <p class="text-xs font-semibold text-gray-700">Zona {{ $zone->zone }}</p>
                        <p class="text-xs font-bold {{ $textColor }}">{{ $pct }}%</p>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ number_format($zone->total_fill) }} / {{ number_format($zone->total_capacity) }}</p>
                </div>
            @empty
                <div class="flex-1 flex items-center justify-center">
                    <p class="text-xs text-gray-400 text-center">Belum ada data lokasi.</p>
                </div>
            @endforelse
        </div>
        <a href="{{ route('admin.cbs.mapping') }}"
           class="mt-4 w-full py-2 rounded-lg bg-gray-50 border border-gray-200 text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all text-center block">
            Lihat Mapping Storage
        </a>
    </div>
</div>

{{-- ───────────────────────────────────────────────
     Second Charts Row: Masuk saja | Keluar saja
────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Chart 2: Barang Masuk --}}
    <div class="glass-card p-5 rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Trend Barang Masuk</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['period_label'] }}</p>
            </div>
            <a href="{{ route('admin.reports.incoming') }}" class="text-xs text-primary hover:underline font-medium">Laporan →</a>
        </div>
        <div class="h-36 w-full">
            <canvas id="incomingChart"></canvas>
        </div>
    </div>

    {{-- Chart 3: Barang Keluar --}}
    <div class="glass-card p-5 rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Trend Barang Keluar</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['period_label'] }}</p>
            </div>
            <a href="{{ route('admin.reports.outgoing') }}" class="text-xs text-primary hover:underline font-medium">Laporan →</a>
        </div>
        <div class="h-36 w-full">
            <canvas id="outgoingChart"></canvas>
        </div>
    </div>
</div>

{{-- ───────────────────────────────────────────────
     Chart.js Scripts
────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($stats['chart_days']);
    const labels   = chartData.map(d => d.date);
    const incoming = chartData.map(d => d.incoming);
    const outgoing = chartData.map(d => d.outgoing);

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    };

    // ── Chart 1: Masuk vs Keluar ──────────────────────────────
    new Chart(document.getElementById('flowChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Masuk',
                    data: incoming,
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: '#3B82F6',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Keluar',
                    data: outgoing,
                    backgroundColor: 'rgba(249,115,22,0.7)',
                    borderColor: '#F97316',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: { ...commonOptions, plugins: { legend: { display: false } } }
    });

    // ── Chart 2: Barang Masuk (area) ──────────────────────────
    new Chart(document.getElementById('incomingChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Masuk',
                data: incoming,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59,130,246,0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#3B82F6',
            }]
        },
        options: { ...commonOptions }
    });

    // ── Chart 3: Barang Keluar (area) ────────────────────────
    new Chart(document.getElementById('outgoingChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Keluar',
                data: outgoing,
                borderColor: '#F97316',
                backgroundColor: 'rgba(249,115,22,0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#F97316',
            }]
        },
        options: { ...commonOptions }
    });
});
</script>
@endsection
