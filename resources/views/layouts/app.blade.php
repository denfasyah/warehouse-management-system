<!DOCTYPE html>
<html class="light" lang="id" style="">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Sistem Manajemen Gudang')</title>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        "sidebar-width": "220px",
                        "container-padding": "20px",
                        "gutter": "16px",
                        "card-gap": "16px",
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
                        "headline-md": ["16px", {"lineHeight": "24px", "fontWeight": "600"}],
                        "body-md": ["13px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-lg": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "data-display": ["22px", {"lineHeight": "28px", "fontWeight": "700"}],
                        "label-md": ["11px", {"lineHeight": "16px", "letterSpacing": "0.04em", "fontWeight": "600"}],
                        "headline-lg": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "headline-xl": ["22px", {"lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "700"}]
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
        .sidebar-gradient {
            background: linear-gradient(180deg, #181c61 0%, #2f3478 100%);
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e8eaf0;
            box-shadow: 0px 1px 4px rgba(0,0,0,0.04), 0px 4px 12px rgba(47,52,120,0.04);
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
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body x-data="{ sidebarOpen: false }" class="text-on-background bg-surface-container-lowest antialiased">
    <!-- SideNavBar Partial -->
    @include('partials.sidebar')

    <!-- Main Content Area -->
    <main class="md:ml-sidebar-width min-h-screen flex flex-col transition-all duration-300">
        <!-- TopAppBar Partial -->
        @include('partials.topbar')

        <div class="p-4 md:p-5 space-y-4">
            @yield('content')
        </div>
    </main>
</body>
</html>
