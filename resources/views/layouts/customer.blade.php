<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title') - Housing UMY</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#93251f", // Maroon
                        "secondary": "#F7B800", // Gold
                        "success": "#004029", // Green
                        "background-light": "#f8f6f6",
                        "background-dark": "#201312",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar-active {
            background-color: #93251f;
            color: white;
        }
        .sidebar-active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white h-screen overflow-hidden flex">
    
    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="w-64 bg-white dark:bg-[#1a0f0e] border-r border-slate-200 dark:border-slate-800 flex flex-col h-full shrink-0 z-20 hidden md:flex transition-all duration-300 fixed md:relative">
        <div class="p-6 flex items-center gap-3">
            <div class="size-10 rounded-full bg-primary flex items-center justify-center text-white shrink-0">
                <span class="material-symbols-outlined">apartment</span>
            </div>
            <div class="flex flex-col">
                <h1 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Housing UMY</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Dashboard Mahasiswa</p>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-4 flex flex-col gap-2 overflow-y-auto">
            <a class="{{ request()->routeIs('customer.dashboard') ? 'sidebar-active' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-colors group" href="{{ route('customer.dashboard') }}">
                <span class="material-symbols-outlined">home</span>
                <span class="text-sm font-medium">Beranda</span>
            </a>
            <a class="{{ request()->routeIs('customer.reservation*') ? 'sidebar-active' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-colors group" href="#">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="text-sm font-medium">Reservasi Saya</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group" href="#">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm font-medium">Profil</span>
            </a>
            <!--
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group" href="#">
                <span class="material-symbols-outlined flex-shrink-0">notifications</span>
                <span class="text-sm font-medium flex-1">Notifikasi</span>
                <span class="flex items-center justify-center size-5 rounded-full bg-secondary text-white text-[10px] font-bold">3</span>
            </a>
            -->
        </nav>
        
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            <form action="{{ route('customer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto relative w-full">
        <!-- Mobile Header (Visible only on small screens) -->
        <div class="md:hidden flex items-center justify-between p-4 bg-white dark:bg-[#1a0f0e] border-b border-slate-200 dark:border-slate-800 sticky top-0 z-10">
            <div class="flex items-center gap-2">
                <div class="size-8 rounded-full bg-primary flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-[18px]">apartment</span>
                </div>
                <span class="font-bold text-slate-900 dark:text-white">Housing UMY</span>
            </div>
            <button id="mobile-menu-btn" class="text-slate-600 dark:text-slate-300">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>

        @yield('content')
        
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-10 hidden md:hidden"></div>

    <script>
        // Mobile Menu Toggle logic
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('hidden');
            overlay.classList.toggle('hidden');
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        }
    </script>
</body>
</html>
