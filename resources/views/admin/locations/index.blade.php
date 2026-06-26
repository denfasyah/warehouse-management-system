@extends('layouts.app')
@section('title', 'Lokasi Penyimpanan - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Lokasi Penyimpanan</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola zona dan rak penyimpanan berdasarkan Class-Based Storage.</p>
    </div>
    <button x-data x-on:click="$dispatch('open-modal', 'create-location')" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 shadow-sm transition-all active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Tambah Lokasi
    </button>
</div>

<!-- Card Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Kode Lokasi</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Kelas (CBS)</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Kapasitas (Terisi)</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($locations as $index => $location)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold tracking-widest
                                {{ $location->is_bulk_zone ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $location->code }}
                            </span>
                            @if($location->is_bulk_zone)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold rounded">BULK</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase">Zone: {{ $location->zone }} | Rack: {{ $location->rack }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold 
                            {{ $location->storage_class == 'fast' ? 'bg-red-50 text-red-700 border-red-200' : 
                               ($location->storage_class == 'medium' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 
                               ($location->storage_class == 'slow' ? 'bg-green-50 text-green-700 border-green-200' : 
                               ($location->storage_class == 'general' ? 'bg-blue-50 text-blue-700 border-blue-200' :
                               'bg-gray-50 text-gray-700 border-gray-200'))) }} border">
                            {{ $location->storage_class == 'general' ? 'Bulk / Umum' : ucfirst($location->storage_class) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-between text-xs mb-1">
                            @if($location->is_bulk_zone)
                                <span class="font-medium text-blue-700">{{ number_format($location->current_fill) }} Item (Bulk)</span>
                                <span class="text-blue-500">Unlimited</span>
                            @else
                                <span class="font-medium text-gray-700">{{ $location->current_fill }} / {{ $location->capacity }} Item</span>
                                <span class="text-gray-500">{{ $location->fill_percentage }}%</span>
                            @endif
                        </div>
                        @if(!$location->is_bulk_zone)
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $location->fill_percentage >= 90 ? 'bg-red-500' : ($location->fill_percentage >= 70 ? 'bg-yellow-400' : 'bg-primary') }}" style="width: {{ $location->fill_percentage }}%"></div>
                        </div>
                        @else
                        <div class="w-full bg-blue-100 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-blue-400" style="width: 10%"></div>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($location->is_active)
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
                            x-on:click="$dispatch('open-modal', 'edit-location-{{ $location->id }}')" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary hover:bg-primary/10 transition-colors"
                            title="Edit"
                        >
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="inline-block" id="delete-form-{{ $location->id }}">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="button" 
                                onclick="confirmDelete('delete-form-{{ $location->id }}')" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus"
                            >
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <x-modal name="edit-location-{{ $location->id }}" title="Edit Lokasi">
                    <form action="{{ route('admin.locations.update', $location->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Zona <span class="text-red-500">*</span></label>
                                <input type="text" name="zone" value="{{ old('zone', $location->zone) }}" required maxlength="50" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm uppercase" placeholder="Contoh: A">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Rak <span class="text-red-500">*</span></label>
                                <input type="number" name="rack" value="{{ old('rack', intval($location->rack)) }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: 1">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Bin / Tingkat <span class="text-red-500">*</span></label>
                                <input type="number" name="bin" value="{{ old('bin', intval($location->bin)) }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: 1">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas Storage <span class="text-red-500">*</span></label>
                                <select name="storage_class" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                                    <option value="fast" {{ $location->storage_class == 'fast' ? 'selected' : '' }}>Fast Moving</option>
                                    <option value="medium" {{ $location->storage_class == 'medium' ? 'selected' : '' }}>Medium Moving</option>
                                    <option value="slow" {{ $location->storage_class == 'slow' ? 'selected' : '' }}>Slow Moving</option>
                                    <option value="general" {{ $location->storage_class == 'general' ? 'selected' : '' }}>General (Umum)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kapasitas Maksimal <span class="text-red-500">*</span></label>
                                <input type="number" name="capacity" value="{{ old('capacity', $location->capacity) }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Opsional...">{{ old('description', $location->description) }}</textarea>
                        </div>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="is_active" id="is_active_{{ $location->id }}" value="1" {{ $location->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                            <label for="is_active_{{ $location->id }}" class="ml-2 text-sm text-gray-700">Lokasi Aktif (Siap Digunakan)</label>
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
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">shelves</span>
                            <p>Belum ada data lokasi rak.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<x-modal name="create-location" title="Tambah Lokasi Rak Baru">
    <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Zona <span class="text-red-500">*</span></label>
                <input type="text" name="zone" value="{{ old('zone') }}" required maxlength="50" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm uppercase" placeholder="Contoh: A">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Rak <span class="text-red-500">*</span></label>
                <input type="number" name="rack" value="{{ old('rack') }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: 1">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bin / Tingkat <span class="text-red-500">*</span></label>
                <input type="number" name="bin" value="{{ old('bin') }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Contoh: 1">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas Storage <span class="text-red-500">*</span></label>
                <select name="storage_class" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                    <option value="" disabled selected>Pilih Kelas...</option>
                    <option value="fast" {{ old('storage_class') == 'fast' ? 'selected' : '' }}>Fast Moving</option>
                    <option value="medium" {{ old('storage_class') == 'medium' ? 'selected' : '' }}>Medium Moving</option>
                    <option value="slow" {{ old('storage_class') == 'slow' ? 'selected' : '' }}>Slow Moving</option>
                    <option value="general" {{ old('storage_class') == 'general' ? 'selected' : '' }}>General (Umum)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kapasitas Maksimal <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', 100) }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm" placeholder="Opsional...">{{ old('description') }}</textarea>
        </div>
        <div class="flex items-center mt-2">
            <input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
            <label for="is_active_new" class="ml-2 text-sm text-gray-700">Lokasi Aktif (Siap Digunakan)</label>
        </div>
        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-6">
            <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors">Simpan Lokasi</button>
        </div>
    </form>
</x-modal>

<script>
function confirmDelete(formId) {
    Swal.fire({
        title: 'Hapus Lokasi Rak?',
        text: "Pastikan lokasi ini sudah kosong dan tidak menyimpan barang apapun.",
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
