@extends('layouts.app')
@section('title', 'Riwayat Aktivitas - WMS')
@section('role', 'PETUGAS')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="space-y-5 max-w-5xl mx-auto">

    {{-- Header --}}
    <div>
        <h1 class="text-headline-lg font-bold text-on-surface">Riwayat Aktivitas</h1>
        <p class="text-body-md text-on-surface-variant mt-1">Daftar semua aktivitas penerimaan dan pengeluaran barang yang Anda lakukan.</p>
    </div>

    {{-- Filter Card --}}
    <div class="glass-card rounded-2xl p-5">
        <form method="GET" action="{{ route('petugas.activities.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-col gap-1 min-w-[150px]">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis Transaksi</label>
                <select name="type" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Penerimaan Barang</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Pengeluaran Barang</option>
                </select>
            </div>

            <div class="flex flex-col gap-1 min-w-[145px]">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="flex flex-col gap-1 min-w-[145px]">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex items-center gap-1.5 bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span> Filter
                </button>
                @if(request()->anyFilled(['type', 'date_from', 'date_to']))
                    <a href="{{ route('petugas.activities.index') }}" class="flex items-center gap-1.5 bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">close</span> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- List --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($activities->isEmpty())
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-5xl text-gray-300 block mb-3" style="font-variation-settings:'FILL' 1;">history_toggle_off</span>
                <h3 class="text-base font-bold text-gray-700">Tidak Ada Aktivitas</h3>
                <p class="text-gray-500 mt-1 text-sm">Tidak ada aktivitas yang sesuai dengan filter.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($activities as $activity)
                    <div class="flex items-start gap-4 p-5 hover:bg-gray-50/70 transition-colors">
                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $activity['type'] == 'in' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' 1;">
                                {{ $activity['type'] == 'in' ? 'move_to_inbox' : 'outbox' }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $activity['type'] == 'in' ? 'text-blue-600' : 'text-purple-600' }}">
                                        {{ $activity['title'] }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-0.5">ID: <span class="font-semibold text-gray-600">{{ $activity['id'] }}</span></p>
                                </div>
                                <div class="flex flex-col items-start sm:items-end gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $activity['status_color'] == 'green' ? 'bg-green-100 text-green-700' : ($activity['status_color'] == 'red' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $activity['status'] }}
                                    </span>
                                    <p class="text-[11px] text-gray-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        {{ $activity['created_at']->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-2.5 flex flex-wrap gap-4 text-xs text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-gray-400">inventory_2</span>
                                    <span class="font-semibold text-gray-800">{{ $activity['item'] }}</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-gray-400">tag</span>
                                    <span class="font-semibold text-gray-800">{{ $activity['quantity'] }}</span> Qty
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-gray-400">shelves</span>
                                    <span class="font-semibold text-gray-800">{{ $activity['location'] }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
