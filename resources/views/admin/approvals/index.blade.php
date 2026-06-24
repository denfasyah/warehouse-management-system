@extends('layouts.app')
@section('title', 'Persetujuan Barang Keluar - WMS Admin')
@section('role', 'ADMINISTRATOR')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Persetujuan Barang Keluar</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola dan tinjau permintaan pengeluaran stok dari Petugas Gudang.</p>
    </div>
    
    <!-- Filter Status -->
    <div class="flex gap-2 bg-white rounded-xl shadow-sm border border-gray-100 p-1">
        <a href="{{ route('admin.approvals.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'text-gray-500 hover:bg-gray-50' }}">
            Menunggu
        </a>
        <a href="{{ route('admin.approvals.index', ['status' => 'approved']) }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $status === 'approved' ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-gray-50' }}">
            Disetujui
        </a>
        <a href="{{ route('admin.approvals.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $status === 'rejected' ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-gray-50' }}">
            Ditolak
        </a>
        <a href="{{ route('admin.approvals.index', ['status' => 'all']) }}" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $status === 'all' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50' }}">
            Semua
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm">{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm">{{ session('error') }}</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Tgl Request & Petugas</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Detail Barang</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Permintaan</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Tujuan</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Aksi / Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($approvals as $req)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $req->requested_at->format('d M Y H:i') }}</p>
                        <div class="flex items-center gap-1.5 mt-1 text-gray-500">
                            <span class="material-symbols-outlined text-[16px]">person</span>
                            <span class="text-xs">{{ $req->requestedBy->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-800">{{ $req->item->name }}</p>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $req->item->sku }}</p>
                        <div class="flex gap-2 mt-1.5">
                            <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-bold">Rak: {{ $req->location->code }}</span>
                            <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded font-bold border border-emerald-100">Sisa Stok: {{ $req->item->stock }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-lg font-bold text-amber-600">-{{ $req->quantity }}</span>
                        <span class="text-xs text-gray-500">{{ $req->item->unit }}</span>
                        @if($req->quantity > $req->item->stock)
                            <div class="mt-1 flex items-center justify-center gap-1 text-red-500">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                <span class="text-[10px] font-bold">Melebihi Stok!</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800 font-medium">{{ $req->destination }}</p>
                        @if($req->note)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2" title="{{ $req->note }}">"{{ $req->note }}"</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.approvals.approve', $req->id) }}" method="POST" class="inline approve-form">
                                    @csrf
                                    <button type="button" class="btn-approve w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center hover:bg-emerald-200 transition-colors shadow-sm" title="Setujui">
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->item->name) }}')" class="w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200 transition-colors shadow-sm" title="Tolak">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        @elseif($req->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-xs font-bold uppercase tracking-wider border border-green-200">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Approved
                            </span>
                        @elseif($req->status === 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-bold uppercase tracking-wider border border-red-200" title="{{ $req->reject_reason }}">
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
                                <span class="material-symbols-outlined text-3xl text-gray-400">check_box</span>
                            </div>
                            <h3 class="text-gray-800 font-bold mb-1">Tidak Ada Data</h3>
                            <p class="text-gray-500 text-sm">Belum ada permohonan dengan status {{ $status }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($approvals->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $approvals->links() }}
    </div>
    @endif
</div>

<!-- Modal Tolak Permintaan -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Tolak Permintaan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Anda akan menolak pengajuan keluar untuk barang <strong id="rejectItemName" class="text-gray-800"></strong>. Silakan isi alasan penolakan di bawah agar petugas mengetahuinya.</p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="reject_reason" id="rejectReasonInput" rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 text-sm" placeholder="Contoh: Stok sedang di-hold untuk proyek lain." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Ya, Tolak Permintaan
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRejectModal(id, itemName) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectItemName').textContent = itemName;
        // Update form action dynamically
        document.getElementById('rejectForm').action = `/admin/approvals/${id}/reject`;
        
        // Auto focus
        setTimeout(() => {
            document.getElementById('rejectReasonInput').focus();
        }, 100);
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectReasonInput').value = '';
    }

    // SweetAlert for Approve Action
    document.querySelectorAll('.btn-approve').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            Swal.fire({
                title: 'Setujui Permintaan?',
                text: "Stok akan langsung terpotong dari gudang.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669', // emerald-600
                cancelButtonColor: '#9ca3af', // gray-400
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-6',
                    cancelButton: 'rounded-xl font-medium px-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
