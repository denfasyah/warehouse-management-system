@extends('layouts.app')
@section('title', 'Manajemen Petugas - WMS Admin')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-headline-lg font-bold text-gray-800">Manajemen Petugas</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola akun petugas gudang dan sektor penugasan mereka.</p>
    </div>
    <button x-data x-on:click="$dispatch('open-modal', 'create-user')" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 shadow-sm transition-all active:scale-95">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
        Tambah Petugas
    </button>
</div>

<!-- Card Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">Nama & Email</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-center">Status Akun</th>
                    <th class="px-6 py-4 text-xs font-label-md text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-50 overflow-hidden border border-blue-100 shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E5EEFF&color=181c61" class="w-full h-full object-cover" alt="{{ $user->name }}">
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                            <p class="text-[11px] text-gray-500">{{ $user->email }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button 
                            x-data 
                            x-on:click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary hover:bg-primary/10 transition-colors"
                            title="Edit / Reset Password"
                        >
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" id="delete-form-{{ $user->id }}">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="button" 
                                onclick="confirmDelete('delete-form-{{ $user->id }}')" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus Akun"
                            >
                                <span class="material-symbols-outlined text-[18px]">person_remove</span>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <x-modal name="edit-user-{{ $user->id }}" title="Edit Petugas">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                        </div>
                        
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-xs font-semibold text-blue-800 mb-2">Ubah Password (Opsional)</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <input type="password" name="password" placeholder="Password Baru" class="w-full rounded-md border-blue-200 focus:border-blue-400 focus:ring focus:ring-blue-200 text-xs">
                                </div>
                                <div>
                                    <input type="password" name="password_confirmation" placeholder="Ulangi Password" class="w-full rounded-md border-blue-200 focus:border-blue-400 focus:ring focus:ring-blue-200 text-xs">
                                </div>
                            </div>
                            <p class="text-[10px] text-blue-600 mt-1">Biarkan kosong jika tidak ingin mengubah password.</p>
                        </div>

                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="is_active" id="is_active_{{ $user->id }}" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                            <label for="is_active_{{ $user->id }}" class="ml-2 text-sm text-gray-700">Akun Aktif (Dapat Login)</label>
                        </div>
                        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-6">
                            <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors">Simpan Perubahan</button>
                        </div>
                    </form>
                </x-modal>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">group_off</span>
                            <p>Belum ada data petugas gudang.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<x-modal name="create-user" title="Tambah Akun Petugas Baru">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
        </div>
        
        <div class="grid grid-cols-2 gap-4 mt-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ulangi Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
            </div>
        </div>

        <div class="flex items-center mt-2">
            <input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
            <label for="is_active_new" class="ml-2 text-sm text-gray-700">Akun Aktif (Dapat Login)</label>
        </div>
        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 mt-6">
            <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors">Buat Akun Petugas</button>
        </div>
    </form>
</x-modal>

<script>
function confirmDelete(formId) {
    Swal.fire({
        title: 'Hapus Petugas?',
        text: "Akun akan dihapus dari sistem. Jika petugas memiliki riwayat transaksi, akun hanya akan dinonaktifkan.",
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
