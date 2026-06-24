@extends('layouts.app')
@section('title', 'Halaman Tidak Ditemukan - WMS')

@section('sidebar')
    <!-- Sembunyikan sidebar di halaman error -->
@endsection

@section('content')
<div class="flex flex-col justify-center items-center h-full min-h-[60vh] text-center">
    <div class="w-32 h-32 bg-primary/5 rounded-full flex items-center justify-center mb-6">
        <span class="material-symbols-outlined text-6xl text-primary" style="font-variation-settings: 'FILL' 1;">error</span>
    </div>
    <h1 class="text-4xl font-black text-gray-800 tracking-tight mb-2">404</h1>
    <h2 class="text-xl font-bold text-gray-600 mb-4">Halaman Tidak Ditemukan</h2>
    <p class="text-gray-500 max-w-md mb-8">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan. Silakan kembali ke dashboard.</p>
    
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-container hover:shadow-lg transition-all active:scale-95">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        Kembali ke Dashboard
    </a>
</div>
@endsection
