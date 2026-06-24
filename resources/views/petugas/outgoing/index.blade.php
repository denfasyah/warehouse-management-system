@extends('layouts.app')
@section('title', 'Permintaan Barang Keluar - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Permintaan Barang Keluar</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat pengajuan pengeluaran barang yang menunggu persetujuan Admin.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('petugas.outgoing.create') }}" class="bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 hover:bg-primary/90 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add_box</span>
            Buat Permintaan Keluar
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
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Tgl Request</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Jumlah</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Tujuan</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($outgoingGoods as $out)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $out->requested_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $out->requested_at->format('H:i') }} WIB</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $out->item->name }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $out->item->sku }} &bull; Rak: {{ $out->location->code }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-amber-600">-{{ $out->quantity }}</span>
                        <span class="text-xs text-gray-500">{{ $out->item->unit }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800 font-medium">{{ $out->destination }}</p>
                        @if($out->note)
                            <p class="text-[11px] text-gray-500 truncate max-w-[200px]" title="{{ $out->note }}">{{ $out->note }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($out->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-yellow-50 text-yellow-700 text-xs font-bold uppercase tracking-wider border border-yellow-200">
                                <span class="material-symbols-outlined text-[14px]">hourglass_empty</span> Pending
                            </span>
                        @elseif($out->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-xs font-bold uppercase tracking-wider border border-green-200" title="Disetujui oleh: {{ $out->approvedBy->name ?? '-' }} pada {{ $out->processed_at?->format('d M Y H:i') }}">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Approved
                            </span>
                        @elseif($out->status === 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-bold uppercase tracking-wider border border-red-200" title="Alasan: {{ $out->reject_reason }}">
                                <span class="material-symbols-outlined text-[14px]">cancel</span> Rejected
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-gray-400">outbox</span>
                            </div>
                            <h3 class="text-gray-800 font-bold mb-1">Belum Ada Permintaan</h3>
                            <p class="text-gray-500 text-sm">Anda belum mengajukan pengeluaran barang sama sekali.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($outgoingGoods->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $outgoingGoods->links() }}
    </div>
    @endif
</div>
@endsection
