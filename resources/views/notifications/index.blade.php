@extends('layouts.app')
@section('title', 'Semua Notifikasi - WMS')
@section('role', auth()->user()->role === 'admin' ? 'ADMINISTRATOR' : 'PETUGAS GUDANG')

@section('sidebar')
    @if(auth()->user()->role === 'admin')
        @include('partials.sidebar_menu_admin')
    @else
        @include('partials.sidebar_menu_petugas')
    @endif
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Semua Notifikasi</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat lengkap aktivitas dan pemberitahuan sistem.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($notifications->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($notifications as $notification)
                @php
                    $isApproved = $notification->type === 'approved';
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                @endphp
                <div class="p-6 hover:bg-gray-50 transition-colors flex gap-4">
                    <div class="flex-shrink-0 mt-1">
                        @if($isApproved)
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                                <span class="material-symbols-outlined text-[24px]">check_circle</span>
                            </div>
                        @elseif($notification->type === 'rejected')
                            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center border border-red-100">
                                <span class="material-symbols-outlined text-[24px]">cancel</span>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100">
                                <span class="material-symbols-outlined text-[24px]">notifications</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-800">{{ $notification->title ?? 'Pemberitahuan' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                
                                @if(isset($data['reject_reason']) && $data['reject_reason'])
                                    <div class="mt-2 p-3 bg-red-50 rounded-lg border border-red-100 text-sm text-red-700 italic">
                                        "{{ $data['reject_reason'] }}"
                                    </div>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs text-gray-500 block">{{ $notification->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 font-medium uppercase">{{ $notification->created_at->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $notifications->links() }}
        </div>
        @endif
    @else
        <div class="py-20 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl text-gray-300">notifications_paused</span>
            </div>
            <h3 class="text-gray-800 font-bold mb-1">Belum Ada Notifikasi</h3>
            <p class="text-gray-500 text-sm">Semua riwayat notifikasi Anda akan muncul di sini.</p>
        </div>
    @endif
</div>
@endsection
