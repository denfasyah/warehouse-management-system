<!DOCTYPE html>
<html class="light" lang="id" style="">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login - Sistem Manajemen Gudang</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary-container": "#fdc425",
                        "tertiary-fixed-dim": "#c4c7c9",
                        "surface": "#f8f9ff",
                        "on-tertiary-fixed-variant": "#444749",
                        "surface-dim": "#cbdbf5",
                        "inverse-primary": "#bec2ff",
                        "on-primary-fixed-variant": "#3b4084",
                        "on-surface": "#0b1c30",
                        "primary-container": "#2f3478",
                        "surface-bright": "#f8f9ff",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed-dim": "#f7be1d",
                        "surface-tint": "#53589e",
                        "secondary-fixed": "#ffdf9a",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#eaf1ff",
                        "surface-container": "#e5eeff",
                        "on-secondary-container": "#6d5200",
                        "surface-container-high": "#dce9ff",
                        "on-secondary": "#ffffff",
                        "primary-fixed": "#e0e0ff",
                        "secondary": "#785a00",
                        "on-primary-container": "#9aa0eb",
                        "on-error": "#ffffff",
                        "on-secondary-fixed": "#251a00",
                        "on-error-container": "#93000a",
                        "error": "#ba1a1a",
                        "inverse-surface": "#213145",
                        "on-background": "#0b1c30",
                        "surface-container-low": "#eff4ff",
                        "on-primary-fixed": "#0b0f58",
                        "tertiary": "#232628",
                        "surface-variant": "#d3e4fe",
                        "error-container": "#ffdad6",
                        "primary": "#181c61",
                        "tertiary-container": "#393c3e",
                        "on-secondary-fixed-variant": "#5a4300",
                        "primary-fixed-dim": "#bec2ff",
                        "outline-variant": "#c7c5d2",
                        "background": "#f8f9ff",
                        "on-surface-variant": "#464650",
                        "tertiary-fixed": "#e0e3e5",
                        "surface-container-highest": "#d3e4fe",
                        "outline": "#777681",
                        "on-tertiary-container": "#a3a6a8",
                        "on-tertiary-fixed": "#191c1e"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "sidebar-width": "260px",
                        "container-padding": "24px",
                        "gutter": "20px",
                        "card-gap": "24px",
                        "base": "8px"
                    },
                    "fontFamily": {
                        "headline-md": ["Hanken Grotesk"],
                        "body-md": ["Hanken Grotesk"],
                        "body-lg": ["Hanken Grotesk"],
                        "data-display": ["Hanken Grotesk"],
                        "label-md": ["Hanken Grotesk"],
                        "headline-lg": ["Hanken Grotesk"],
                        "headline-xl": ["Hanken Grotesk"]
                    },
                    "fontSize": {
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "data-display": ["28px", {"lineHeight": "32px", "fontWeight": "700"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "headline-lg": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-xl": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Hanken Grotesk', sans-serif;
            background-color: #f8f9ff;
        }
        .login-card-shadow {
            box-shadow: 0px 4px 20px rgba(47, 52, 120, 0.05);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="h-screen w-screen overflow-hidden flex items-center justify-center p-4 md:p-8">
<main class="w-full max-w-[1000px] grid grid-cols-1 md:grid-cols-2 bg-surface-container-lowest rounded-2xl overflow-hidden login-card-shadow h-auto md:h-[600px]">
    <!-- Background Section (Modern Warehouse Illustration) -->
    <div class="hidden md:flex relative flex-col justify-end p-12 bg-primary overflow-hidden group">
        <!-- Background Image -->
        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-30 group-hover:opacity-40 transition-opacity duration-500" alt="Warehouse">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-secondary-container opacity-20 rounded-full -mr-20 -mt-20 mix-blend-overlay"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-container opacity-40 rounded-full -ml-32 -mb-32"></div>
        <div class="relative z-10 space-y-4">
            <h2 class="text-white font-headline-lg font-bold">WMS v2.0</h2>
            <p class="text-white/80 font-body-md">Sistem Manajemen Gudang terintegrasi dengan Class-Based Storage.</p>
            <div class="flex gap-4 pt-4">
                <div class="h-1 w-12 bg-secondary-container rounded-full"></div>
                <div class="h-1 w-4 bg-white/30 rounded-full"></div>
                <div class="h-1 w-4 bg-white/30 rounded-full"></div>
            </div>
        </div>
    </div>
    <!-- Login Card Section -->
    <div class="flex flex-col justify-center px-8 md:px-20 py-12 bg-surface-container-lowest">
        <!-- Logo & Brand -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center shadow-lg">
                <span class="material-symbols-outlined text-white text-3xl" data-icon="inventory_2" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
            </div>
            <div>
                <h1 class="font-headline-lg text-headline-lg text-primary leading-tight">PT. IndoOne Sentosa Indah Abadi</h1>
            </div>
        </div>
        <!-- Header Text -->
        <div class="mb-8">
            <h3 class="font-headline-md text-headline-md text-on-surface">Selamat Datang Kembali</h3>
            <p class="font-body-md text-body-md text-on-surface-variant">Silakan masuk ke akun Anda untuk melanjutkan operasional.</p>
        </div>
        <!-- Login Form -->
        <form class="space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <!-- Username Field -->
            <div class="space-y-2">
                <label class="block font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="email">Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline" data-icon="email">email</span>
                    <input class="w-full pl-12 pr-4 py-3 bg-surface border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md text-on-surface outline-none @error('email') border-red-500 @enderror" id="email" name="email" placeholder="Masukkan email Anda" type="email" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="block font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="password">Password</label>
                    <a class="text-primary font-label-md text-label-md hover:underline decoration-2 underline-offset-4" href="#">Lupa Password?</a>
                </div>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline" data-icon="lock">lock</span>
                    <input class="w-full pl-12 pr-12 py-3 bg-surface border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md text-on-surface outline-none @error('password') border-red-500 @enderror" id="password" name="password" placeholder="••••••••" type="password" required>
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors" type="button" onclick="const p=document.getElementById('password'); p.type=p.type==='password'?'text':'password'; this.querySelector('span').textContent=p.type==='password'?'visibility':'visibility_off';">
                        <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Remember Me -->
            <div class="flex items-center">
                <input class="w-4 h-4 text-primary bg-surface border-outline-variant rounded focus:ring-primary" id="remember" name="remember" type="checkbox">
                <label class="ml-2 font-body-md text-body-md text-on-surface-variant select-none" for="remember">Ingat saya di perangkat ini</label>
            </div>
            <!-- Submit Button -->
            <button class="w-full bg-primary hover:bg-primary-container text-white font-headline-md text-headline-md py-4 rounded-xl shadow-md active:scale-95 transition-all duration-150 flex items-center justify-center gap-2 group" type="submit">
                Masuk Ke Dasbor
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform" data-icon="arrow_forward">arrow_forward</span>
            </button>
        </form>
        <!-- Footer Section -->
        <div class="mt-auto pt-10 text-center border-t border-surface-variant">
            <p class="mt-8 font-label-md text-[10px] text-outline uppercase tracking-[0.2em]">© 2024 - PT. IndoOne Sentosa Indah Abadi</p>
        </div>
    </div>
</main>
</body>
</html>
