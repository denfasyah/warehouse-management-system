@extends('layouts.app')
@section('title', 'Laporan Barang Masuk - Admin Panel')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Barang Masuk</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat penerimaan barang masuk ke gudang.</p>
    </div>
    <a href="{{ route('admin.reports.incoming.pdf', request()->all()) }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
        Export PDF
    </a>
</div>

<div class="glass-card p-5 rounded-xl mb-6">
    <form action="{{ route('admin.reports.incoming') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50 px-3 py-2">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50 px-3 py-2">
        </div>
        <div>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors h-[42px] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
        </div>
        @if(request('start_date') || request('end_date'))
        <div>
            <a href="{{ route('admin.reports.incoming') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm font-semibold transition-colors h-[42px] flex items-center gap-2">
                Reset
            </a>
        </div>
        @endif
    </form>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-sm border border-white/40">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-600">
                <tr>
                    <th class="py-3 px-4 font-semibold">Waktu Masuk</th>
                    <th class="py-3 px-4 font-semibold">SKU / Nama Barang</th>
                    <th class="py-3 px-4 font-semibold">Jumlah</th>
                    <th class="py-3 px-4 font-semibold">Petugas Penerima</th>
                    <th class="py-3 px-4 font-semibold">Lokasi Simpan</th>
                    <th class="py-3 px-4 font-semibold">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white/30">
                @forelse($incomings as $incoming)
                <tr class="hover:bg-white/60 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-bold text-gray-800">{{ $incoming->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $incoming->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-gray-800">{{ $incoming->item->sku }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $incoming->item->name }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                            +{{ number_format($incoming->quantity) }} {{ $incoming->item->unit }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                                {{ substr($incoming->user->name ?? '?', 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-700">{{ $incoming->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        @if($incoming->location)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                <span class="material-symbols-outlined text-[14px]">shelves</span>
                                {{ $incoming->location->code }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-xs text-gray-500 max-w-[200px] truncate" title="{{ $incoming->note }}">
                        {{ $incoming->note ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl text-gray-400">history</span>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data barang masuk pada periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
