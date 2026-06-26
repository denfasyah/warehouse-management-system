@extends('layouts.app')
@section('title', 'Klasifikasi CBS - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Klasifikasi Class-Based Storage</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar barang beserta skor frekuensi dan kelas penyimpanannya.</p>
    </div>
    <form action="{{ route('admin.cbs.recalculate') }}" method="POST">
        @csrf
        <button type="submit" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-bold inline-flex items-center gap-2 shadow-sm transition-all active:scale-95" onclick="return confirm('Jalankan kalkulasi CBS sekarang? Proses ini akan menghitung ulang seluruh skor item berdasarkan pengeluaran 30 hari terakhir.')">
            <span class="material-symbols-outlined text-[20px]">calculate</span>
            Jalankan Kalkulasi Sekarang
        </button>
    </form>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

<!-- Filter -->
<div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-2 overflow-x-auto">
    <a href="{{ route('admin.cbs.classification') }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ !request('class') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Semua Kelas</a>
    <a href="{{ route('admin.cbs.classification', ['class' => 'fast']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ request('class') == 'fast' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">Fast Moving</a>
    <a href="{{ route('admin.cbs.classification', ['class' => 'medium']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ request('class') == 'medium' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">Medium Moving</a>
    <a href="{{ route('admin.cbs.classification', ['class' => 'slow']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ request('class') == 'slow' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">Slow Moving</a>
    <a href="{{ route('admin.cbs.classification', ['class' => 'unclassified']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ request('class') == 'unclassified' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Unclassified</a>
</div>

<!-- Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">SKU / Item</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Score Keluar (30 Hr)</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Kelas CBS</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Rekomendasi Relokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $index => $item)
                @php
                    $isMismatch = $item->is_location_mismatch;
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors {{ $isMismatch ? 'bg-orange-50/30' : '' }}">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $items->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-mono font-bold">{{ $item->sku }}</span>
                        <p class="font-bold text-gray-800 mt-2">{{ $item->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Lokasi saat ini: 
                            <span class="font-bold">{{ $item->locations->pluck('code')->join(', ') ?: 'Belum diatur' }}</span>
                        </p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-2xl font-bold font-mono text-gray-800">{{ number_format($item->frequency_score) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center min-w-[100px] px-3 py-1.5 rounded-full text-xs font-bold uppercase
                            {{ $item->storage_class == 'fast' ? 'bg-red-100 text-red-700' : 
                               ($item->storage_class == 'medium' ? 'bg-yellow-100 text-yellow-700' : 
                               ($item->storage_class == 'slow' ? 'bg-green-100 text-green-700' : 
                               'bg-gray-100 text-gray-600')) }}">
                            {{ $item->storage_class }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($isMismatch)
                            <span class="inline-flex flex-col items-center gap-1 px-3 py-2 bg-orange-50 text-orange-700 border border-orange-200 rounded-lg text-xs font-semibold">
                                <span class="material-symbols-outlined text-[20px]">warning</span>
                                Perlu Relokasi
                            </span>
                        @else
                            <span class="text-xs text-gray-400 font-medium">Sesuai (Match)</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">inventory</span>
                        <p>Belum ada data barang.</p>
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
@endsection
