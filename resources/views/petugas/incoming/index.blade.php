@extends('layouts.app')
@section('title', 'Barang Masuk (Inbound) - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Riwayat Barang Masuk</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar barang yang Anda catat masuk ke gudang.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('petugas.incoming.create') }}" class="bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 hover:bg-primary/90 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add_box</span>
            Input Manual
        </a>
        <a href="{{ route('petugas.scanner.index') }}" class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 hover:bg-emerald-700 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">barcode_scanner</span>
            Mode Scanner
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm">{{ session('success') }}</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Lokasi Masuk</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Jumlah</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($incomingGoods as $in)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $in->received_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $in->received_at->format('H:i') }} WIB</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $in->item->name }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $in->item->sku }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-surface-container-high text-on-surface rounded-md text-xs font-semibold">{{ $in->location->code }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-emerald-600">+{{ $in->quantity }}</span>
                        <span class="text-xs text-gray-500">{{ $in->item->unit }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $in->note ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-gray-400">inbox</span>
                            </div>
                            <h3 class="text-gray-800 font-bold mb-1">Belum Ada Transaksi</h3>
                            <p class="text-gray-500 text-sm">Anda belum mencatat barang masuk sama sekali.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomingGoods->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $incomingGoods->links() }}
    </div>
    @endif
</div>
@endsection
