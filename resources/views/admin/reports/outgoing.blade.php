@extends('layouts.app')
@section('title', 'Laporan Barang Keluar - Admin Panel')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Barang Keluar</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat pengeluaran barang dari gudang.</p>
    </div>
    <a href="{{ route('admin.reports.outgoing.pdf', request()->all()) }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
        Export PDF
    </a>
</div>

<div class="glass-card p-5 rounded-xl mb-6">
    <form action="{{ route('admin.reports.outgoing') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50 px-3 py-2">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50 px-3 py-2">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary bg-white/50">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors h-[42px] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
        </div>
        @if(request('start_date') || request('end_date') || request('status'))
        <div>
            <a href="{{ route('admin.reports.outgoing') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm font-semibold transition-colors h-[42px] flex items-center gap-2">
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
                    <th class="py-3 px-4 font-semibold">Waktu Request</th>
                    <th class="py-3 px-4 font-semibold">SKU / Nama Barang</th>
                    <th class="py-3 px-4 font-semibold">Jumlah</th>
                    <th class="py-3 px-4 font-semibold">Pemohon</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white/30">
                @forelse($outgoings as $outgoing)
                <tr class="hover:bg-white/60 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-bold text-gray-800">{{ $outgoing->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $outgoing->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-gray-800">{{ $outgoing->item->sku }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $outgoing->item->name }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">
                            -{{ number_format($outgoing->quantity) }} {{ $outgoing->item->unit }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-medium text-gray-700">{{ $outgoing->requestedBy->name ?? 'Unknown' }}</span>
                    </td>
                    <td class="py-3 px-4">
                        @if($outgoing->status == 'approved')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-bold bg-green-100 text-green-700">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Disetujui
                            </span>
                        @elseif($outgoing->status == 'rejected')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-bold bg-red-100 text-red-700">
                                <span class="material-symbols-outlined text-[14px]">cancel</span> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-bold bg-yellow-100 text-yellow-700">
                                <span class="material-symbols-outlined text-[14px]">hourglass_empty</span> Pending
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($outgoing->approvedBy)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold">
                                    {{ substr($outgoing->approvedBy->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $outgoing->approvedBy->name }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl text-gray-400">outbox</span>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data barang keluar pada periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
