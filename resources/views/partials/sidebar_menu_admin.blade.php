{{-- === DASHBOARD === --}}
<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/5 hover:text-white' }} rounded-lg text-sm transition-all">
    <span class="material-symbols-outlined text-[18px]" {{ request()->routeIs('admin.dashboard') ? 'style=font-variation-settings:\'FILL\'_1;' : '' }}>dashboard</span>
    <span>Dashboard</span>
</a>

{{-- === MASTER DATA === --}}
<div x-data="{ open: {{ request()->is('admin/categories*') || request()->is('admin/locations*') || request()->is('admin/items*') || request()->is('admin/users*') ? 'true' : 'false' }} }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">database</span>
        <span class="flex-1 text-left">Master Data</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: {{ request()->is('admin/categories*') || request()->is('admin/locations*') || request()->is('admin/items*') || request()->is('admin/users*') ? 'block' : 'none' }};" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="{{ route('admin.items.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('admin.items.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">inventory_2</span> Data Barang
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('admin.categories.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">category</span> Kategori Barang
        </a>
        <a href="{{ route('admin.locations.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('admin.locations.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">shelves</span> Lokasi Penyimpanan
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('admin.users.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">manage_accounts</span> Manajemen User
        </a>
    </div>
</div>

{{-- === CLASS-BASED STORAGE === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">grid_view</span>
        <span class="flex-1 text-left">Class-Based Storage</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">sort</span> Klasifikasi Barang
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">map</span> Mapping Storage
        </a>
    </div>
</div>

{{-- === PERSETUJUAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">approval</span>
        <span class="flex-1 text-left">Persetujuan</span>
        {{-- Badge notifikasi --}}
        <span class="ml-auto mr-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full leading-none">3</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">task_alt</span> Approval Barang Keluar
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">history</span> Riwayat Persetujuan
        </a>
    </div>
</div>

{{-- === LAPORAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">analytics</span>
        <span class="flex-1 text-left">Laporan</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">inventory</span> Laporan Stok
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">move_to_inbox</span> Lap. Barang Masuk
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">outbox</span> Lap. Barang Keluar
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">warehouse</span> Laporan Storage
        </a>
    </div>
</div>

{{-- === PENGATURAN === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all group">
        <span class="material-symbols-outlined text-[18px]">settings</span>
        <span class="flex-1 text-left">Pengaturan</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">tune</span> Threshold CBS
        </a>
    </div>
</div>
