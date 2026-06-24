@extends('layouts.app')
@section('title', 'Kategori Barang - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Kategori Barang</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar klasifikasi dan kategori barang di gudang.</p>
    </div>
    <button x-data x-on:click="$dispatch('open-modal', 'create-category')" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 shadow-sm transition-all active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Tambah Kategori
    </button>
</div>

<!-- Card Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Total Item</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $index => $category)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-mono font-bold tracking-widest">{{ $category->code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-800">{{ $category->name }}</p>
                        @if($category->description)
                            <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $category->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $category->items_count }} Barang
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($category->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button 
                            x-data 
                            x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary hover:bg-primary/10 transition-colors"
                            title="Edit"
                        >
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block" id="delete-form-{{ $category->id }}">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="button" 
                                onclick="confirmDelete('delete-form-{{ $category->id }}')" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus"
                            >
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <x-modal name="edit-category-{{ $category->id }}" title="Edit Kategori">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Kategori <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $category->code) }}" required maxlength="10" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 uppercase font-mono text-sm" placeholder="Contoh: ELK">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: Elektronik">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Opsional...">{{ old('description', $category->description) }}</textarea>
                        </div>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="is_active" id="is_active_{{ $category->id }}" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                            <label for="is_active_{{ $category->id }}" class="ml-2 text-sm text-gray-700">Kategori Aktif</label>
                        </div>
                        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-6">
                            <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors">Simpan Perubahan</button>
                        </div>
                    </form>
                </x-modal>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">category</span>
                            <p>Belum ada data kategori.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<x-modal name="create-category" title="Tambah Kategori Baru">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code') }}" required maxlength="10" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 uppercase font-mono text-sm" placeholder="Contoh: ELK">
            <p class="text-[11px] text-gray-500 mt-1">Digunakan untuk auto-generate SKU barang.</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: Elektronik">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Opsional...">{{ old('description') }}</textarea>
        </div>
        <div class="flex items-center mt-2">
            <input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
            <label for="is_active_new" class="ml-2 text-sm text-gray-700">Kategori Aktif</label>
        </div>
        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-6">
            <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors">Simpan Kategori</button>
        </div>
    </form>
</x-modal>

<script>
function confirmDelete(formId) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Kategori yang dihapus tidak dapat dikembalikan. Pastikan tidak ada barang di kategori ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba1a1a',
        cancelButtonColor: '#e0e3e5',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: '<span class="text-gray-700">Batal</span>',
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
