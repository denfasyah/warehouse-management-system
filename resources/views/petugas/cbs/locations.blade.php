@extends('layouts.app')
@section('title', 'Lokasi Penyimpanan - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Lokasi Penyimpanan</h2>
    <p class="text-sm text-gray-500 mt-1">Peta kapasitas rak gudang berdasarkan sistem Class-Based Storage (CBS).</p>
</div>

<!-- Legenda Warna -->
<div class="mb-6 flex flex-wrap gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-red-100 border border-red-300"></div>
        <span class="text-sm text-gray-700 font-medium">Fast Moving</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300"></div>
        <span class="text-sm text-gray-700 font-medium">Medium Moving</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-green-100 border border-green-300"></div>
        <span class="text-sm text-gray-700 font-medium">Slow Moving</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 rounded bg-blue-100 border border-blue-300"></div>
        <span class="text-sm text-gray-700 font-medium">Bulk / Umum</span>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    @forelse($zones as $zoneName => $locs)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h3 class="font-bold text-lg text-gray-800">Zona {{ $zoneName }}</h3>
                <span class="text-xs text-gray-500 font-medium">{{ $locs->count() }} Rak</span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($locs as $loc)
                    @php
                        // Color styling based on storage class
                        $bgClass = 'bg-gray-50 border-gray-200 text-gray-700';
                        $barClass = 'bg-gray-400';
                        
                        if ($loc->storage_class == 'fast') {
                            $bgClass = 'bg-red-50 border-red-200 text-red-800';
                            $barClass = 'bg-red-500';
                        } elseif ($loc->storage_class == 'medium') {
                            $bgClass = 'bg-yellow-50 border-yellow-200 text-yellow-800';
                            $barClass = 'bg-yellow-500';
                        } elseif ($loc->storage_class == 'slow') {
                            $bgClass = 'bg-green-50 border-green-200 text-green-800';
                            $barClass = 'bg-green-500';
                        } elseif ($loc->storage_class == 'general') {
                            $bgClass = 'bg-blue-50 border-blue-200 text-blue-800';
                            $barClass = 'bg-blue-500';
                        }
                    @endphp

                    <div x-data="{ open: false }" class="relative">
                        <!-- Kotak Rak -->
                        <div x-on:click="open = !open" x-on:click.outside="open = false" 
                             class="border-2 rounded-xl p-3 cursor-pointer hover:shadow-md transition-all {{ $bgClass }} flex flex-col items-center justify-center text-center h-28 relative group overflow-hidden">
                            
                            <span class="font-mono font-bold text-lg leading-none z-10">{{ $loc->code }}</span>
                            
                            @if($loc->is_bulk_zone)
                                <span class="text-[10px] mt-1 font-semibold opacity-75 z-10">BULK</span>
                                <!-- Unlimited bar style -->
                                <div class="absolute bottom-0 left-0 w-full h-1.5 bg-blue-100">
                                    <div class="h-full bg-blue-400" style="width: 100%"></div>
                                </div>
                            @else
                                <span class="text-[10px] mt-1 font-semibold opacity-75 z-10">{{ $loc->fill_percentage }}% Terisi</span>
                                <!-- Capacity Bar -->
                                <div class="absolute bottom-0 left-0 w-full h-1.5 bg-white/50">
                                    <div class="h-full {{ $barClass }}" style="width: {{ $loc->fill_percentage }}%"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Popup Detail -->
                        <div x-show="open" x-transition class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-4 z-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800 font-mono">{{ $loc->code }}</h4>
                                    <p class="text-[10px] text-gray-500 uppercase">{{ $loc->storage_class == 'general' ? 'Bulk/Umum' : $loc->storage_class }}</p>
                                </div>
                                <button x-on:click="open = false" class="text-gray-400 hover:text-gray-600"><span class="material-symbols-outlined text-sm">close</span></button>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-600">Kapasitas</span>
                                    <span class="font-bold text-gray-800">{{ $loc->is_bulk_zone ? 'Unlimited' : ($loc->current_fill . '/' . $loc->capacity) }}</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-700 mb-1 border-b pb-1">Daftar Barang ({{ $loc->items->count() }})</p>
                                <div class="max-h-32 overflow-y-auto pr-1 space-y-1">
                                    @forelse($loc->items as $item)
                                        <div class="text-[10px] flex justify-between p-1 bg-gray-50 rounded">
                                            <span class="font-medium text-gray-700 truncate" title="{{ $item->name }}">{{ $item->name }}</span>
                                            <span class="font-mono text-gray-500 ml-1">{{ $item->pivot->quantity }}</span>
                                        </div>
                                    @empty
                                        <p class="text-[10px] text-gray-400 italic text-center py-2">Kosong</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">grid_off</span>
            <h3 class="text-lg font-bold text-gray-700 mb-1">Belum ada lokasi rak</h3>
            <p class="text-sm text-gray-500">Silakan tambahkan data lokasi rak melalui menu Data Master > Lokasi.</p>
        </div>
    @endforelse
</div>
@endsection
