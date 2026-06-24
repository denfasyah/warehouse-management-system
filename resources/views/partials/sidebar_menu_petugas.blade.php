{{-- === DASHBOARD === --}}
<a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 {{ request()->routeIs('petugas.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/5 hover:text-white font-medium' }} rounded-lg text-sm transition-all">
    <span class="material-symbols-outlined text-[18px]" style="{{ request()->routeIs('petugas.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
    <span>Dashboard</span>
</a>

{{-- === TRANSAKSI === --}}
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all">
        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
        <span class="flex-1 text-left">Transaksi</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="{{ route('petugas.incoming.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('petugas.incoming.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">move_to_inbox</span> Barang Masuk
        </a>
        <a href="{{ route('petugas.outgoing.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 {{ request()->routeIs('petugas.outgoing.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/65 hover:text-white hover:bg-white/8 font-medium' }} rounded-md text-[13px] transition-all">
            <span class="material-symbols-outlined text-[16px]">outbox</span> Barang Keluar
        </a>
    </div>
</div>

{{-- === STORAGE === --}}
<div x-data="{ open: false }" class="mt-1">
    <button @click="open = !open" class="w-full flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all">
        <span class="material-symbols-outlined text-[18px]">shelves</span>
        <span class="flex-1 text-left">Storage</span>
        <span class="material-symbols-outlined text-[15px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5">
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">location_on</span> Lokasi Penyimpanan
        </a>
        <a href="#" class="flex items-center gap-2 px-2.5 py-1.5 text-white/65 hover:text-white hover:bg-white/8 rounded-md text-[13px] transition-all font-medium">
            <span class="material-symbols-outlined text-[16px]">move_item</span> Penataan Barang
        </a>
    </div>
</div>

{{-- === RIWAYAT AKTIVITAS === --}}
<a href="#" class="mt-1 flex items-center gap-2.5 px-3 py-2 text-white/60 hover:bg-white/5 hover:text-white rounded-lg text-sm transition-all font-medium">
    <span class="material-symbols-outlined text-[18px]">history</span>
    <span>Riwayat Aktivitas</span>
</a>
