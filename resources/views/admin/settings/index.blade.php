@extends('layouts.app')
@section('title', 'Pengaturan CBS - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Pengaturan Threshold CBS</h2>
    <p class="text-sm text-gray-500 mt-1">Konfigurasi batas frekuensi (batas minimum quantity) untuk penentuan kelas Fast, Medium, dan Slow Moving.</p>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-start gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @foreach($settings as $setting)
            <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex-1">
                        <label class="block font-bold text-gray-800 text-base mb-1">{{ $setting->label }}</label>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $setting->description }}</p>
                    </div>
                    <div class="w-full md:w-48 shrink-0">
                        <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 font-mono text-center font-bold text-lg p-3">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined">save</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
