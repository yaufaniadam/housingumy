<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Housing UMY') - Reservasi Kamar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('booking.index') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-bold text-xl">Housing UMY</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('booking.rooms') }}" class="hover:bg-blue-700 px-3 py-2 rounded-lg transition">Cari Kamar</a>
                    @if(Auth::guard('customer')->check())
                        <a href="{{ route('customer.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded-lg transition">Dashboard</a>
                        <div class="flex items-center gap-2">
                            <span class="text-blue-100 text-sm">{{ Auth::guard('customer')->user()->name }}</span>
                            <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('booking.check') }}" class="hover:bg-blue-700 px-3 py-2 rounded-lg transition">Cek Reservasi</a>
                        <a href="{{ route('customer.login') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">Housing UMY</h3>
                    <p class="text-gray-400">Sistem Manajemen Akomodasi Universitas Muhammadiyah Yogyakarta</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Kontak</h3>
                    <p class="text-gray-400">Kampus Terpadu UMY</p>
                    <p class="text-gray-400">Jl. Brawijaya, Kasihan, Bantul</p>
                    <p class="text-gray-400">Yogyakarta 55183</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Gedung</h3>
                    <ul class="text-gray-400 space-y-1">
                        <li>University Resident</li>
                        <li>Ma'had Ali</li>
                        <li>Wisma Pascasarjana</li>
                        <li>Professor Guest House</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Housing UMY. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
