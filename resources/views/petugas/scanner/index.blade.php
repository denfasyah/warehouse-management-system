@extends('layouts.app')
@section('title', 'Mode Scanner Barcode - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('petugas.incoming.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors text-gray-600">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Scanner Barcode</h2>
            <p class="text-sm text-gray-500 mt-0.5">Fokuskan kursor pada input di bawah, lalu scan barcode barang.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-w-3xl mx-auto text-center mt-10">
    <div class="w-24 h-24 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-[48px]">barcode_scanner</span>
    </div>
    
    <h3 class="text-xl font-bold text-gray-800 mb-2">Siap Menerima Scan</h3>
    <p class="text-gray-500 text-sm mb-8 max-w-md mx-auto">Gunakan alat scanner barcode. Pastikan alat sudah terhubung dan lampu scanner menyala.</p>
    
    <div class="relative max-w-md mx-auto mb-4">
        <input type="text" id="scannerInput" class="w-full text-center text-xl font-mono tracking-widest py-4 px-6 rounded-2xl border-2 border-emerald-500 focus:ring focus:ring-emerald-200 focus:outline-none shadow-inner" placeholder="Pindai Barcode di Sini..." autofocus autocomplete="off">
        <div id="loadingIndicator" class="absolute inset-y-0 right-4 flex items-center hidden">
            <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
    <p class="text-xs text-gray-400 font-medium" id="statusText">Aplikasi otomatis menekan "Enter" setelah pemindaian selesai.</p>
</div>

<!-- Modal Konfirmasi Input Barang -->
<div id="scanModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('petugas.incoming.store') }}" method="POST" id="scanForm">
                @csrf
                <input type="hidden" name="item_id" id="modalItemId">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-emerald-600">inventory_2</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modalTitle">Barang Ditemukan</h3>
                            
                            <div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <h4 class="font-bold text-gray-800 text-lg" id="modalItemName">-</h4>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="text-xs bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded font-mono" id="modalItemSku">SKU: -</span>
                                    <span class="text-xs bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded" id="modalItemCat">Kategori: -</span>
                                </div>
                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-200">
                                    <span class="material-symbols-outlined text-gray-400 text-[18px]">shelves</span>
                                    <span class="text-sm font-semibold text-gray-700" id="modalItemLoc">Rak: -</span>
                                </div>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Masuk</label>
                                    <div class="flex items-center gap-3">
                                        <input type="number" name="quantity" id="modalQuantity" min="1" value="1" class="w-1/2 text-lg font-bold text-center rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 py-2" required>
                                        <span class="text-gray-500 font-medium" id="modalItemUnit">pcs</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                                    <input type="text" name="note" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" placeholder="Kondisi barang, dll...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Simpan Barang
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Audio Feedback -->
<audio id="audioSuccess" src="https://cdn.pixabay.com/download/audio/2021/08/04/audio_0625c1539c.mp3?filename=success-1-6297.mp3" preload="auto"></audio>
<audio id="audioError" src="https://cdn.pixabay.com/download/audio/2022/03/10/audio_c8c8a73467.mp3?filename=error-126627.mp3" preload="auto"></audio>

<!-- SweetAlert2 untuk error not found -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const scannerInput = document.getElementById('scannerInput');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const statusText = document.getElementById('statusText');
    const scanModal = document.getElementById('scanModal');
    
    // Audio
    const audioSuccess = document.getElementById('audioSuccess');
    const audioError = document.getElementById('audioError');

    // Pastikan input selalu fokus saat berada di halaman ini
    document.addEventListener('click', (e) => {
        if (!scanModal.classList.contains('hidden')) return;
        scannerInput.focus();
    });

    scannerInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code.length > 0) {
                processBarcode(code);
            }
        }
    });

    function processBarcode(code) {
        scannerInput.disabled = true;
        loadingIndicator.classList.remove('hidden');
        statusText.textContent = "Mencari data barang...";
        
        fetch(`{{ route('petugas.scanner.api') }}?code=${encodeURIComponent(code)}`)
            .then(response => response.json())
            .then(data => {
                scannerInput.disabled = false;
                loadingIndicator.classList.add('hidden');
                scannerInput.value = '';
                
                if (data.success) {
                    // Mainkan suara sukses
                    audioSuccess.currentTime = 0;
                    audioSuccess.play().catch(e => console.log('Audio play blocked:', e));
                    
                    openModal(data.item);
                } else {
                    // Mainkan suara error
                    audioError.currentTime = 0;
                    audioError.play().catch(e => console.log('Audio play blocked:', e));
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: 'Barang dengan barcode/SKU tersebut tidak ada di sistem.',
                        confirmButtonColor: '#181c61',
                    }).then(() => {
                        scannerInput.focus();
                        statusText.textContent = 'Siap memindai...';
                    });
                }
            })
            .catch(error => {
                scannerInput.disabled = false;
                loadingIndicator.classList.add('hidden');
                scannerInput.value = '';
                scannerInput.focus();
                statusText.textContent = 'Terjadi kesalahan jaringan.';
                console.error(error);
            });
    }

    function openModal(item) {
        document.getElementById('modalItemId').value = item.id;
        document.getElementById('modalItemName').textContent = item.name;
        document.getElementById('modalItemSku').textContent = 'SKU: ' + item.sku;
        document.getElementById('modalItemCat').textContent = 'Kategori: ' + item.category_name;
        document.getElementById('modalItemLoc').textContent = 'Rak: ' + item.location_code;
        document.getElementById('modalItemUnit').textContent = item.unit;
        
        // Reset form input
        document.getElementById('modalQuantity').value = '1';
        document.querySelector('input[name="note"]').value = '';
        
        // Tampilkan modal
        scanModal.classList.remove('hidden');
        
        // Auto focus ke quantity setelah 100ms
        setTimeout(() => {
            const qtyInput = document.getElementById('modalQuantity');
            qtyInput.focus();
            qtyInput.select();
        }, 100);
    }

    function closeModal() {
        scanModal.classList.add('hidden');
        scannerInput.focus();
        statusText.textContent = 'Siap memindai...';
    }
</script>
@endsection
