@extends('layouts.app')
@section('title', 'Buat Permintaan Keluar - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('petugas.outgoing.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors text-gray-600">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Permintaan Barang Keluar</h2>
        <p class="text-sm text-gray-500 mt-0.5">Pengajuan distribusi barang. Stok tidak akan berkurang sebelum Admin menyetujui.</p>
    </div>
</div>

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm">{{ session('error') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Kolom Kiri: Pencarian Barang -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-lg text-gray-800 mb-4">Pilih Barang yang Akan Keluar</h3>
        
        <div class="relative mb-4">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400">search</span>
            </div>
            <input type="text" id="searchInput" class="w-full pl-10 rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-500/20 text-sm py-2.5" placeholder="Ketik Nama Barang, SKU, atau Barcode..." autocomplete="off">
        </div>

        <div id="searchResults" class="hidden flex-col gap-2 max-h-[400px] overflow-y-auto">
            <!-- Hasil pencarian via JS -->
        </div>
        
        <div id="searchEmpty" class="hidden text-center py-8">
            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">inventory_2</span>
            <p class="text-gray-500 text-sm">Barang tidak ditemukan.</p>
        </div>
        <div id="searchIdle" class="text-center py-8">
            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">manage_search</span>
            <p class="text-gray-500 text-sm">Mulai ketikkan kata kunci untuk mencari stok yang tersedia.</p>
        </div>
    </div>

    <!-- Kolom Kanan: Form Input -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('petugas.outgoing.store') }}" method="POST">
            @csrf
            
            <input type="hidden" name="item_id" id="selectedItemId" value="{{ old('item_id') }}">

            <div class="mb-6 pb-6 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Detail Permintaan</h3>
                
                <div id="selectedItemDisplay" class="{{ old('item_id') ? '' : 'hidden' }} bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-4 items-start">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-amber-600 shadow-sm">
                        <span class="material-symbols-outlined text-2xl">outbox</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800" id="displayItemName">Pilih barang terlebih dahulu...</h4>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="text-xs bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded font-mono" id="displayItemSku">SKU: -</span>
                            <span class="text-xs bg-surface-container-high text-on-surface px-2 py-1 rounded font-semibold" id="displayItemLocation">Lokasi Rak: -</span>
                            <span class="text-xs bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded" id="displayItemStock">Sisa Stok: -</span>
                        </div>
                    </div>
                </div>
                
                <div id="noItemDisplay" class="{{ old('item_id') ? 'hidden' : '' }} border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                    <p class="text-gray-500 text-sm">Pilih barang dari kolom pencarian di sebelah kiri terlebih dahulu.</p>
                </div>
                @error('item_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Dikeluarkan (Qty) <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="quantity" id="inputQuantity" min="1" value="{{ old('quantity') }}" placeholder="Contoh: 10" class="w-full md:w-1/2 rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-500/20 text-sm py-2.5">
                        <span class="text-gray-500 text-sm font-medium" id="displayItemUnit">pcs</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="qtyHelperText">Pastikan tidak melebihi sisa stok.</p>
                    @error('quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tujuan Pengiriman <span class="text-red-500">*</span></label>
                    <input type="text" name="destination" value="{{ old('destination') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-500/20 text-sm py-2.5" placeholder="Contoh: Proyek Sudirman Tower B">
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="note" rows="2" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-500/20 text-sm" placeholder="Contoh: Permintaan urgent dari mandor.">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" id="btnSubmit" class="w-full bg-amber-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-amber-700 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed" {{ old('item_id') ? '' : 'disabled' }}>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchEmpty = document.getElementById('searchEmpty');
    const searchIdle = document.getElementById('searchIdle');
    
    // Hidden inputs & display elements
    const selectedItemId = document.getElementById('selectedItemId');
    const selectedItemDisplay = document.getElementById('selectedItemDisplay');
    const noItemDisplay = document.getElementById('noItemDisplay');
    const btnSubmit = document.getElementById('btnSubmit');
    const inputQuantity = document.getElementById('inputQuantity');
    
    const displayItemName = document.getElementById('displayItemName');
    const displayItemSku = document.getElementById('displayItemSku');
    const displayItemLocation = document.getElementById('displayItemLocation');
    const displayItemStock = document.getElementById('displayItemStock');
    const displayItemUnit = document.getElementById('displayItemUnit');

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            searchResults.classList.remove('flex');
            searchEmpty.classList.add('hidden');
            searchIdle.classList.remove('hidden');
            return;
        }

        searchIdle.classList.add('hidden');
        
        debounceTimer = setTimeout(() => {
            fetch(`{{ route('petugas.incoming.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    // Filter out items with 0 stock
                    const availableItems = data.filter(item => item.stock > 0);
                    
                    if (availableItems.length === 0) {
                        searchResults.classList.add('hidden');
                        searchResults.classList.remove('flex');
                        searchEmpty.classList.remove('hidden');
                    } else {
                        searchEmpty.classList.add('hidden');
                        searchResults.classList.remove('hidden');
                        searchResults.classList.add('flex');
                        
                        availableItems.forEach(item => {
                            const locationCode = item.locations_codes || '-';
                            const categoryName = item.category ? item.category.name : '-';
                            
                            const div = document.createElement('div');
                            div.className = 'p-3 border border-gray-100 rounded-lg hover:border-amber-500 hover:bg-amber-50 cursor-pointer transition-colors flex justify-between items-center';
                            div.innerHTML = `
                                <div>
                                    <p class="text-sm font-bold text-gray-800">${item.name}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">${item.sku} &bull; Stok: <span class="font-bold text-emerald-600">${item.stock}</span></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded font-semibold text-gray-700">Rak: ${locationCode}</span>
                                </div>
                            `;
                            
                            div.addEventListener('click', () => {
                                selectItem(item);
                            });
                            
                            searchResults.appendChild(div);
                        });
                    }
                })
                .catch(err => console.error(err));
        }, 300);
    });

    function selectItem(item) {
        // Set value to hidden input
        selectedItemId.value = item.id;
        
        // Enable submit button
        btnSubmit.removeAttribute('disabled');
        
        // Limit max quantity to stock
        inputQuantity.setAttribute('max', item.stock);
        
        // Hide/Show display
        noItemDisplay.classList.add('hidden');
        selectedItemDisplay.classList.remove('hidden');
        
        // Update DOM
        displayItemName.textContent = item.name;
        displayItemSku.textContent = 'SKU: ' + item.sku;
        displayItemLocation.textContent = 'Lokasi: ' + (item.locations_codes || '-');
        displayItemStock.textContent = 'Sisa Stok: ' + item.stock;
        displayItemStock.className = 'text-xs bg-emerald-50 border border-emerald-200 text-emerald-700 px-2 py-1 rounded font-bold';
        displayItemUnit.textContent = item.unit;
        
        // Clear search
        searchInput.value = '';
        searchResults.innerHTML = '';
        searchResults.classList.add('hidden');
        searchResults.classList.remove('flex');
        searchIdle.classList.remove('hidden');
        
        // Focus on quantity
        inputQuantity.focus();
    }
</script>
@endsection
