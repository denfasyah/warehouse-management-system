@extends('layouts.app')
@section('title', 'Tugas Relokasi - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Pemantauan Tugas Relokasi</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar tugas pemindahan fisik barang yang sedang berjalan maupun sudah selesai.</p>
    </div>
    <a href="{{ route('admin.cbs.classification') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary border border-primary/30 bg-primary/5 rounded-xl hover:bg-primary/10 transition-all">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Klasifikasi
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="glass-card p-4 rounded-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 shrink-0">
            <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div>
            <p class="text-[11px] text-gray-400 uppercase tracking-wide font-bold">Tugas Pending</p>
            <p class="text-2xl font-bold text-orange-600">{{ $pendingCount }}</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 shrink-0">
            <span class="material-symbols-outlined">task_alt</span>
        </div>
        <div>
            <p class="text-[11px] text-gray-400 uppercase tracking-wide font-bold">Tugas Selesai</p>
            <p class="text-2xl font-bold text-green-600">{{ $completedCount }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold">Barang</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold">Dari Rak</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold">Ke Rak</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold text-center">Qty</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold text-center">Status</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold text-center">Dibuat</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold text-center">Selesai Oleh</th>
                    <th class="px-5 py-4 text-xs text-gray-500 uppercase tracking-wider font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-bold text-gray-800 text-sm">{{ $task->item->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $task->item->sku }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-bold">
                            <span class="material-symbols-outlined text-[14px]">shelves</span>
                            {{ $task->fromLocation->code }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-bold">
                            <span class="material-symbols-outlined text-[14px]">shelves</span>
                            {{ $task->toLocation->code }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="font-bold text-gray-800">{{ number_format($task->quantity) }}</span>
                        <span class="text-xs text-gray-400"> pcs</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($task->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase">Pending</span>
                        @elseif($task->status === 'completed')
                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase">Selesai</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold uppercase">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                        {{ $task->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                        @if($task->completedBy)
                            <span class="font-semibold text-green-700">{{ $task->completedBy->name }}</span>
                            <br><span class="text-gray-400">{{ $task->completed_at?->format('d M H:i') }}</span>
                        @else
                            <span class="text-gray-400 italic">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($task->status === 'pending')
                        <form action="{{ route('admin.cbs.cancelTask', $task) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Batalkan tugas relokasi ini?')" class="px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-all">
                                Batalkan
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">assignment_turned_in</span>
                        <p class="font-semibold">Tidak ada tugas relokasi.</p>
                        <p class="text-sm mt-1">Buat tugas dari halaman Klasifikasi Barang.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tasks->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection
