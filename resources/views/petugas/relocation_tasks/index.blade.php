@extends('layouts.app')
@section('title', 'Tugas Relokasi - WMS Petugas')
@section('role', 'PETUGAS GUDANG')

@section('sidebar')
    @include('partials.sidebar_menu_petugas')
@endsection

@section('content')

{{-- Welcome --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Tugas Relokasi Barang</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar barang yang perlu Anda pindahkan secara fisik di gudang sesuai arahan sistem.</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-xl shrink-0">
        <span class="material-symbols-outlined text-green-600 text-[18px]">task_alt</span>
        <div>
            <p class="text-[10px] text-green-600 font-bold uppercase">Selesai Hari Ini</p>
            <p class="text-xl font-bold text-green-700 leading-none">{{ $completedToday }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm font-medium">{{ session('error') }}</p>
</div>
@endif

{{-- Task List --}}
@forelse($tasks as $task)
<div class="glass-card rounded-2xl mb-4 overflow-hidden border-l-4 border-l-orange-400">
    <div class="p-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="flex-1">
                {{-- Item Info --}}
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold uppercase rounded-md tracking-wide">Perlu Dipindah</span>
                    <span class="text-xs text-gray-400">{{ $task->created_at->diffForHumans() }}</span>
                </div>
                <h3 class="text-lg font-bold text-gray-800">{{ $task->item->name }}</h3>
                <p class="text-xs text-gray-400 font-mono mt-0.5">SKU: {{ $task->item->sku }}</p>

                {{-- Direction Arrow --}}
                <div class="flex items-center gap-3 mt-4">
                    {{-- From --}}
                    <div class="flex flex-col items-center">
                        <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Dari Rak</p>
                        <div class="flex items-center gap-1.5 px-4 py-2.5 bg-red-50 border-2 border-red-200 rounded-xl">
                            <span class="material-symbols-outlined text-red-500 text-[20px]">shelves</span>
                            <span class="text-xl font-black text-red-700">{{ $task->fromLocation->code }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">
                            @if($task->fromLocation->storage_class === 'fast') Zona Fast 🔴
                            @elseif($task->fromLocation->storage_class === 'medium') Zona Medium 🟡
                            @elseif($task->fromLocation->storage_class === 'slow') Zona Slow 🟢
                            @else Zona Umum ⬜
                            @endif
                        </p>
                    </div>

                    {{-- Arrow --}}
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-2xl">→</span>
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ number_format($task->quantity) }} pcs</span>
                    </div>

                    {{-- To --}}
                    <div class="flex flex-col items-center">
                        <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Ke Rak</p>
                        <div class="flex items-center gap-1.5 px-4 py-2.5 bg-green-50 border-2 border-green-300 rounded-xl">
                            <span class="material-symbols-outlined text-green-600 text-[20px]">shelves</span>
                            <span class="text-xl font-black text-green-700">{{ $task->toLocation->code }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">
                            @if($task->toLocation->storage_class === 'fast') Zona Fast 🔴
                            @elseif($task->toLocation->storage_class === 'medium') Zona Medium 🟡
                            @elseif($task->toLocation->storage_class === 'slow') Zona Slow 🟢
                            @else Zona Umum ⬜
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Location Fill Info --}}
                <div class="mt-3 flex gap-3 text-xs text-gray-500">
                    <span>Kapasitas tersisa di rak tujuan: <strong class="text-green-700">{{ number_format($task->toLocation->capacity - $task->toLocation->current_fill) }} pcs</strong></span>
                </div>
            </div>

            {{-- Action Button --}}
            <div class="shrink-0 flex flex-col gap-2">
                <form id="complete-task-{{ $task->id }}" action="{{ route('petugas.relocationTasks.complete', $task) }}" method="POST">
                    @csrf
                    <button type="button"
                        onclick="confirmTask('complete-task-{{ $task->id }}', '{{ $task->quantity }}', '{{ $task->item->name }}', '{{ $task->fromLocation->code }}', '{{ $task->toLocation->code }}')"
                        class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm inline-flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        Selesai Dipindah
                    </button>
                </form>
                <p class="text-[10px] text-gray-400 text-center">Tekan setelah fisik barang dipindah</p>
            </div>
        </div>
    </div>
</div>
@empty
<div class="glass-card rounded-2xl p-16 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300 block mb-3">assignment_turned_in</span>
    <h3 class="font-bold text-gray-600 text-lg">Tidak Ada Tugas Relokasi</h3>
    <p class="text-sm text-gray-400 mt-1">Semua barang sudah berada di tempat yang benar, atau Admin belum membuat tugas relokasi baru.</p>
</div>
@endforelse

@if($tasks->hasPages())
<div class="mt-4">
    {{ $tasks->links() }}
</div>
@endif

<script>
function confirmTask(formId, qty, itemName, fromLoc, toLoc) {
    Swal.fire({
        title: 'Konfirmasi Relokasi',
        html: `Anda sudah memindahkan <b>${qty} pcs ${itemName}</b> dari Rak <b>${fromLoc}</b> ke Rak <b>${toLoc}</b> secara fisik?<br><br><span class="text-xs text-gray-500">Setelah dikonfirmasi, sistem akan otomatis memperbarui lokasi stok barang.</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#181c61', // primary color
        cancelButtonColor: '#e0e3e5',
        confirmButtonText: 'Ya, Sudah Dipindah',
        cancelButtonText: '<span class="text-gray-700">Belum</span>',
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-gray-100',
            title: 'font-headline-md text-gray-800',
            confirmButton: 'rounded-lg px-6',
            cancelButton: 'rounded-lg px-6'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    })
}
</script>

@endsection
