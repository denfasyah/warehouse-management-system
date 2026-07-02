@extends('layouts.app')
@section('title', 'Hasil Pencarian - WMS')

@section('sidebar')
    @if(auth()->user()->role === 'admin')
        @include('partials.sidebar_menu_admin')
    @else
        @include('partials.sidebar_menu_petugas')
    @endif
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-headline-lg font-bold text-gray-800">Hasil Pencarian</h1>
        <p class="text-sm text-gray-500 mt-1">
            Menampilkan hasil untuk kata kunci: <span class="font-semibold text-primary">"{{ $query }}"</span>
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Hasil Barang --}}
        <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">package</span>
                    Data Barang
                </h2>
                <span class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $items->count() }} Ditemukan</span>
            </div>
            
            <div class="p-5 flex-1 max-h-[600px] overflow-y-auto">
                @if($items->isEmpty())
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">inventory_2</span>
                        <p class="text-sm text-gray-500">Tidak ada barang yang cocok dengan pencarian Anda.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($items as $item)
                        <div class="border border-gray-100 rounded-xl p-4 hover:border-primary/30 transition-colors bg-white">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $item->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $item->sku }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold {{ $item->stock <= $item->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $item->stock }} Unit
                                    </p>
                                </div>
                            </div>
                            
                            @if($item->locations->isNotEmpty())
                            <div class="mt-3 pt-3 border-t border-gray-50">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi Penyimpanan:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($item->locations as $loc)
                                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 flex items-center gap-1 font-mono">
                                            <span class="material-symbols-outlined text-[12px]">shelves</span>
                                            {{ $loc->code }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Hasil Lokasi --}}
        <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-500 text-[20px]">shelves</span>
                    Data Lokasi / Rak
                </h2>
                <span class="bg-orange-50 text-orange-600 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $locations->count() }} Ditemukan</span>
            </div>
            
            <div class="p-5 flex-1 max-h-[600px] overflow-y-auto">
                @if($locations->isEmpty())
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">shelves</span>
                        <p class="text-sm text-gray-500">Tidak ada lokasi rak yang cocok dengan pencarian Anda.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($locations as $loc)
                        <div class="border border-gray-100 rounded-xl p-4 hover:border-orange-400/30 transition-colors bg-white">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-blue-700 font-mono">{{ $loc->code }}</h3>
                                        <span class="text-[10px] uppercase font-bold text-gray-400">ZONA {{ $loc->zone }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kelas: {{ ucfirst($loc->storage_class) }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $pct = $loc->capacity > 0 ? round(($loc->current_fill / $loc->capacity) * 100) : 0;
                                        $pctColor = $pct > 80 ? 'text-red-600' : 'text-gray-600';
                                    @endphp
                                    <p class="text-sm font-bold text-gray-700">{{ $loc->current_fill }} / {{ $loc->capacity }}</p>
                                    <p class="text-xs font-bold {{ $pctColor }}">{{ $pct }}% Terisi</p>
                                </div>
                            </div>
                            
                            @if($loc->items->isNotEmpty())
                            <div class="mt-3 pt-3 border-t border-gray-50">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Barang di Lokasi Ini ({{ $loc->items->count() }} Jenis):</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($loc->items->take(5) as $item)
                                        <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded truncate max-w-[150px]" title="{{ $item->name }}">
                                            {{ $item->name }}
                                        </span>
                                    @endforeach
                                    @if($loc->items->count() > 5)
                                        <span class="text-[11px] text-gray-400 px-1 py-0.5">+{{ $loc->items->count() - 5 }} lainnya</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
