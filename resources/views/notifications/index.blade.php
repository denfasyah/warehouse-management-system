@extends('layouts.app')
@section('title', 'Notifikasi - WMS')
@section('role', auth()->user()->role === 'admin' ? 'ADMINISTRATOR' : 'PETUGAS')

@section('sidebar')
    @if(auth()->user()->role === 'admin')
        @include('partials.sidebar_menu_admin')
    @else
        @include('partials.sidebar_menu_petugas')
    @endif
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-headline-lg font-bold text-on-surface">Notifikasi</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Riwayat lengkap pemberitahuan sistem untuk akun Anda.</p>
        </div>
        @if(auth()->user()->unreadNotifications()->exists())
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">done_all</span>
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <div class="glass-card rounded-2xl p-4">
        <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Tab: Status Baca --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                @foreach(['all' => 'Semua', 'unread' => 'Belum Dibaca', 'read' => 'Sudah Dibaca'] as $val => $label)
                    <a href="{{ route('notifications.index', array_merge(request()->query(), ['filter' => $val, 'page' => 1])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $filter === $val ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Filter: Tipe --}}
            <div class="flex flex-col gap-1">
                <select name="type" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary transition-colors">
                    <option value="">Semua Tipe</option>
                    <option value="approved" {{ request('type') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('type') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                </select>
            </div>

            @if(request()->anyFilled(['type']))
                <a href="{{ route('notifications.index', array_diff_key(request()->query(), ['type' => ''])) }}"
                   class="flex items-center gap-1 text-xs text-gray-500 hover:text-primary transition-colors px-2 py-2">
                    <span class="material-symbols-outlined text-[15px]">close</span> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- List --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($notifications->isEmpty())
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300" style="font-variation-settings:'FILL' 1;">notifications_paused</span>
                </div>
                <h3 class="text-gray-700 font-bold text-base mb-1">Tidak Ada Notifikasi</h3>
                <p class="text-gray-400 text-sm">Tidak ada notifikasi yang sesuai dengan filter yang Anda pilih.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($notifications as $notification)
                    @php
                        $isApproved = $notification->type === 'approved';
                        $isRejected = $notification->type === 'rejected';
                        $isUnread   = is_null($notification->read_at);
                        $data       = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4 {{ $isUnread ? 'bg-primary/[0.03]' : '' }} hover:bg-gray-50/70 transition-colors">
                        {{-- Unread dot --}}
                        <div class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $isUnread ? 'bg-primary' : 'bg-transparent' }}"></div>

                        {{-- Icon --}}
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border
                            {{ $isApproved ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($isRejected ? 'bg-red-50 text-red-500 border-red-100' : 'bg-amber-50 text-amber-500 border-amber-100') }}">
                            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                                @if($isApproved) check_circle
                                @elseif($isRejected) cancel
                                @else notifications
                                @endif
                            </span>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-{{ $isUnread ? 'bold' : 'semibold' }} text-gray-800 truncate">
                                        {{ $notification->title ?? ($isApproved ? 'Permintaan Disetujui' : ($isRejected ? 'Permintaan Ditolak' : 'Pemberitahuan')) }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">{{ $notification->message ?? '-' }}</p>

                                    @if(isset($data['reject_reason']) && $data['reject_reason'])
                                        <div class="mt-2 px-3 py-2 bg-red-50 rounded-lg border border-red-100">
                                            <p class="text-xs text-red-700 italic">"{{ $data['reject_reason'] }}"</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-start sm:items-end gap-1.5 shrink-0">
                                    <span class="text-[11px] font-semibold text-gray-500">{{ $notification->created_at->format('d M Y') }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $notification->created_at->format('H:i') }} WIB</span>
                                    <span class="text-[10px] text-gray-400 italic">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
