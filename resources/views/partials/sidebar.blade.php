<!-- Sidebar Partial -->
<div class="relative z-50">
    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" style="display: none;" class="fixed inset-0 bg-on-surface/50 backdrop-blur-sm md:hidden" x-transition.opacity></div>

    <!-- Sidebar Content -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed left-0 top-0 h-screen w-sidebar-width sidebar-gradient flex flex-col py-5 px-3 z-50 text-on-primary md:translate-x-0 transition-transform duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 px-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-secondary-container rounded-lg flex items-center justify-center text-on-secondary-container shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                </div>
                <div>
                    <h1 class="font-headline-md text-[15px] font-black tracking-tight leading-tight">IndoOne Sentosa</h1>
                    <p class="font-label-md text-[10px] opacity-70 uppercase tracking-widest mt-0.5">@yield('role', 'ADMIN PANEL')</p>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-white/50 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 space-y-1 overflow-y-auto custom-scrollbar px-2 md:px-0">
            @yield('sidebar')
        </nav>
        
        <!-- User Profile Area (Bottom Sidebar) -->
        <div class="mt-6 px-2 pt-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-surface-container-high flex-shrink-0 border border-white/20">
                    <img class="w-full h-full rounded-full object-cover" src="https://ui-avatars.com/api/?name=User&background=random" alt="Profile">
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-semibold truncate text-white">User Name</p>
                    <p class="text-[10px] opacity-70 truncate">user@indoone.com</p>
                </div>
            </div>
        </div>
    </aside>
</div>
