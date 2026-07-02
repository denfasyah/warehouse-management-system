@extends('layouts.app')
@section('title', 'Laporan Storage - Admin Panel')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Storage (Kapasitas Gudang)</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau penggunaan kapasitas per lokasi rak penyimpanan.</p>
    </div>
    <a href="{{ route('admin.reports.storage.pdf') }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
        Export PDF
    </a>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-sm border border-white/40">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-600">
                <tr>
                    <th class="py-3 px-4 font-semibold">Kode Lokasi</th>
                    <th class="py-3 px-4 font-semibold">Zona</th>
                    <th class="py-3 px-4 font-semibold">Tingkat (Level)</th>
                    <th class="py-3 px-4 font-semibold">Kapasitas Max</th>
                    <th class="py-3 px-4 font-semibold">Terisi Saat Ini</th>
                    <th class="py-3 px-4 font-semibold">Status Penggunaan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white/30">
                @forelse($locations as $loc)
                @php
                    // Simple capacity calculation assuming $loc->capacity and $loc->items_count or similar exists.
                    // For demo, we just use a placeholder if relations aren't loaded. 
                    // To accurately calculate fill rate we'd need to sum quantities in this location.
                    // Let's assume there's a dynamic property or we just show a badge.
                    // Assuming items() relation exists:
                    $filled = $loc->items->sum('stock');
                    $max = $loc->capacity > 0 ? $loc->capacity : 100; // fallback if capacity 0
                    $pct = min(100, round(($filled / $max) * 100));
                @endphp
                <tr class="hover:bg-white/60 transition-colors">
                    <td class="py-3 px-4">
                        <span class="font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded border border-blue-100">{{ $loc->code }}</span>
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">Zona {{ $loc->zone }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $loc->level }}</td>
                    <td class="py-3 px-4 font-bold text-gray-700">{{ number_format($max) }} Unit</td>
                    <td class="py-3 px-4 font-bold text-gray-700">{{ number_format($filled) }} Unit</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3 w-48">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full {{ $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $pct > 80 ? 'text-red-600' : 'text-gray-600' }}">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl text-gray-400">shelves</span>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data lokasi penyimpanan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
