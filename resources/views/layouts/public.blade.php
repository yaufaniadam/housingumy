<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Housing UMY') - Akomodasi Nyaman</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#93251F",
                        secondary: "#004029",
                        highlight: "#F7B800",
                        "background-light": "#F9FAFB",
                        "background-dark": "#111827",
                        "card-light": "#FFFFFF",
                        "card-dark": "#1F2937",
                        "text-main-light": "#1F2937",
                        "text-main-dark": "#F3F4F6",
                        "text-muted-light": "#6B7280",
                        "text-muted-dark": "#9CA3AF",
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                    }
                },
            },
        };
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563; 
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280; 
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark transition-colors duration-300 font-sans antialiased flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('booking.index') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/30">
                            <span class="material-icons-round text-2xl">apartment</span>
                        </div>
                        <span class="font-display font-bold text-xl sm:text-2xl text-primary dark:text-white tracking-tight">Housing UMY</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors" href="{{ route('booking.check') }}">Cek Reservasi</a>
                    <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors" href="#">Kontak</a>
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors" onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-round dark:hidden">dark_mode</span>
                        <span class="material-icons-round hidden dark:block">light_mode</span>
                    </button>
                    @if(Auth::guard('customer')->check())
                        <a href="{{ route('customer.dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary transition-colors">Dashboard</a>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500">{{ Auth::guard('customer')->user()->name }}</span>
                            <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a class="px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-full hover:bg-red-800 shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5" href="{{ route('customer.login') }}">
                            Login
                        </a>
                    @endif
                </div>
                <div class="md:hidden flex items-center gap-4">
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors" onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-round dark:hidden">dark_mode</span>
                        <span class="material-icons-round hidden dark:block">light_mode</span>
                    </button>
                    <button class="text-gray-600 dark:text-gray-300 hover:text-primary" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <span class="material-icons-round text-3xl">menu</span>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary py-2" href="{{ route('booking.check') }}">Cek Reservasi</a>
                    @if(Auth::guard('customer')->check())
                        <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary py-2" href="{{ route('customer.dashboard') }}">Dashboard</a>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-700 hover:text-primary py-2">Keluar</button>
                        </form>
                    @else
                        <a class="inline-block mt-2 px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-full text-center" href="{{ route('customer.login') }}">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black text-white pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-icons-round text-3xl text-primary">apartment</span>
                        <span class="font-display font-bold text-2xl">Housing UMY</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Sistem Manajemen Akomodasi Universitas Muhammadiyah Yogyakarta. Memberikan pelayanan terbaik untuk mahasiswa dan tamu universitas.
                    </p>
                    <div class="flex space-x-4 pt-2">
                        <a class="text-gray-400 hover:text-white transition-colors" href="#"><i class="material-icons-round text-xl">facebook</i></a>
                        <a class="text-gray-400 hover:text-white transition-colors" href="#"><i class="material-icons-round text-xl">camera_alt</i></a>
                        <a class="text-gray-400 hover:text-white transition-colors" href="#"><i class="material-icons-round text-xl">alternate_email</i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-b border-gray-700 pb-2 inline-block">Kontak</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <span class="material-icons-round text-primary text-lg mt-0.5">location_on</span>
                            <span>Kampus Terpadu UMY<br>Jl. Brawijaya, Kasihan, Bantul<br>Yogyakarta 55183</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-round text-primary text-lg">phone</span>
                            <span>(0274) 387656</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-round text-primary text-lg">email</span>
                            <span>housing@umy.ac.id</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-b border-gray-700 pb-2 inline-block">Gedung</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a class="hover:text-primary transition-colors flex items-center gap-2" href="#"><span class="w-1 h-1 bg-gray-500 rounded-full"></span> University Resident</a></li>
                        <li><a class="hover:text-primary transition-colors flex items-center gap-2" href="#"><span class="w-1 h-1 bg-gray-500 rounded-full"></span> Ma'had Ali</a></li>
                        <li><a class="hover:text-primary transition-colors flex items-center gap-2" href="#"><span class="w-1 h-1 bg-gray-500 rounded-full"></span> Wisma Pascasarjana</a></li>
                        <li><a class="hover:text-primary transition-colors flex items-center gap-2" href="#"><span class="w-1 h-1 bg-gray-500 rounded-full"></span> Professor Guest House</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-b border-gray-700 pb-2 inline-block">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a class="hover:text-primary transition-colors" href="#">Tentang Kami</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Syarat & Ketentuan</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Kebijakan Privasi</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Bantuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-gray-500">© {{ date('Y') }} Housing UMY. All rights reserved.</p>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span>Designed with</span>
                    <span class="material-icons-round text-red-500 text-sm">favorite</span>
                    <span>in Yogyakarta</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
