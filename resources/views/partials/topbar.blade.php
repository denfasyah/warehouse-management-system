<!-- Topbar Partial -->
<header class="flex justify-between items-center px-4 md:px-6 h-16 w-full sticky top-0 z-40 bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100">
    <div class="flex items-center gap-4 flex-1">
        <!-- Mobile hamburger button -->
        <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <div class="relative w-full max-w-md hidden md:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-xl text-gray-400">search</span>
            <input class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all focus:bg-white" placeholder="Cari SKU, nama barang, atau kode rak..." type="text">
        </div>
    </div>
    
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Mobile Search Button -->
        <button class="md:hidden w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">search</span>
        </button>

        <!-- Notifications -->
        <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors relative group">
            <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-surface-container-lowest"></span>
        </button>
        
        <!-- Divider -->
        <div class="h-8 w-[1px] bg-outline-variant/50 hidden md:block mx-1"></div>
        
        <!-- Profile Dropdown Placeholder -->
        <div class="flex items-center gap-3 cursor-pointer hover:bg-surface-container-low p-1.5 rounded-full transition-colors">
            <div class="text-right hidden sm:block">
                <p class="font-bold text-sm text-on-surface leading-none">Budi Santoso</p>
                <p class="text-[10px] text-primary font-bold uppercase mt-1 tracking-wider">@yield('role', 'ADMIN')</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-surface-container-high overflow-hidden border border-outline-variant/30">
                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=E5EEFF&color=181c61" class="w-full h-full object-cover" alt="Profile">
            </div>
            <span class="material-symbols-outlined text-on-surface-variant text-sm hidden md:block">expand_more</span>
        </div>
    </div>
</header>
