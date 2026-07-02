@extends('layouts.app')
@section('title', 'Profil Saya - WMS')
@section('role', auth()->user()->role === 'admin' ? 'ADMINISTRATOR' : 'PETUGAS')

@section('sidebar')
    @if(auth()->user()->role === 'admin')
        @include('partials.sidebar_menu_admin')
    @else
        @include('partials.sidebar_menu_petugas')
    @endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-headline-lg font-bold text-on-surface">Profil Saya</h1>
        <p class="text-body-md text-on-surface-variant mt-1">Informasi akun dan data diri Anda.</p>
    </div>

    {{-- Avatar & Identity Card --}}
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center gap-5 pb-6 border-b border-gray-100">
            <div class="w-20 h-20 rounded-2xl bg-surface-container-high overflow-hidden border-2 border-white shadow-sm shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=E5EEFF&color=181c61"
                     class="w-full h-full object-cover" alt="Profile">
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
                <span class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary rounded-lg text-xs font-bold uppercase tracking-wide">
                    <span class="material-symbols-outlined text-[14px]">badge</span>
                    {{ ucfirst($user->role ?? 'User') }}
                </span>
            </div>
        </div>

        {{-- Info Detail --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-6">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Lengkap</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] text-gray-400">person</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] text-gray-400">mail</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->email }}</span>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Telepon</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] text-gray-400">phone</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->phone ?? '-' }}</span>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Role Akun</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] text-gray-400">manage_accounts</span>
                    <span class="text-sm font-medium text-gray-800">{{ ucfirst($user->role ?? 'User') }}</span>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Akun</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] {{ $user->is_active ? 'text-green-500' : 'text-red-400' }}">
                        {{ $user->is_active ? 'check_circle' : 'cancel' }}
                    </span>
                    <span class="text-sm font-medium {{ $user->is_active ? 'text-green-700' : 'text-red-600' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bergabung Sejak</p>
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="material-symbols-outlined text-[18px] text-gray-400">calendar_month</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Edit form: hanya untuk Admin --}}
        @if(auth()->user()->role === 'admin')
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Ubah Data Profil</h3>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">person</span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('name') border-red-400 @enderror" required>
                        </div>
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">mail</span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('email') border-red-400 @enderror" required>
                        </div>
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Telepon <span class="font-normal text-gray-400">(Opsional)</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">phone</span>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-4">Ubah Password</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Password Baru</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">lock</span>
                                <input type="password" name="password"
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('password') border-red-400 @enderror">
                            </div>
                            @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">lock_reset</span>
                                <input type="password" name="password_confirmation"
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        @else
        {{-- Pesan untuk petugas --}}
        <div class="mt-6 pt-5 border-t border-gray-100">
            <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                <span class="material-symbols-outlined text-amber-500 text-[20px] shrink-0 mt-0.5">info</span>
                <p class="text-sm text-amber-800">Untuk mengubah data profil, silakan hubungi Admin sistem.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
