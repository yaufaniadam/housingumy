@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<header class="relative bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hero-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M0 40L40 0H20L0 20M40 40V20L20 40" fill="none" stroke="white" stroke-width="2"></path>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-pattern)"></rect>
        </svg>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 text-center">
        <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-6xl text-white mb-6 leading-tight">
            Selamat Datang di <br class="hidden sm:block"> Housing UMY
        </h1>
        <p class="text-red-100 text-lg sm:text-xl max-w-2xl mx-auto mb-10 font-light leading-relaxed">
            Temukan akomodasi nyaman dan strategis untuk kebutuhan tinggal Anda di lingkungan kampus Universitas Muhammadiyah Yogyakarta.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a class="px-8 py-3.5 bg-white text-primary font-semibold rounded-full hover:bg-gray-50 shadow-xl transition-all transform hover:-translate-y-1 focus:ring-4 focus:ring-red-300" href="#search">
                Cari Kamar
            </a>
            <a class="px-8 py-3.5 bg-transparent border-2 border-white text-white font-semibold rounded-full hover:bg-white/10 transition-all" href="{{ route('booking.check') }}">
                Cek Reservasi
            </a>
        </div>
    </div>
</header>

<!-- Search Form -->
<div class="relative -mt-12 z-20 px-4 sm:px-6 lg:px-8" id="search">
    <div class="max-w-6xl mx-auto bg-card-light dark:bg-card-dark rounded-2xl shadow-soft p-6 lg:p-8 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-display font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-primary">search</span>
            Cari Ketersediaan
        </h2>
        <form action="{{ route('booking.rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Check-in</label>
                <div class="relative">
                    <input name="check_in" class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10" type="date" value="{{ now()->addDay()->format('Y-m-d') }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-sm">calendar_today</span>
                    </div>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Check-out</label>
                <div class="relative">
                    <input name="check_out" class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10" type="date" value="{{ now()->addDays(2)->format('Y-m-d') }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-sm">event</span>
                    </div>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Gedung</label>
                <select name="building_id" class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Tamu</label>
                <div class="relative">
                    <input name="total_guests" class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10" min="1" type="number" value="1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-sm">person</span>
                    </div>
                </div>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-primary hover:bg-red-800 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors shadow-lg shadow-primary/20 flex justify-center items-center gap-2 h-[42px]" type="submit">
                    <span class="material-icons-round text-sm">search</span>
                    Cari
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Buildings Section -->
<main class="flex-grow pt-16 pb-24 px-4 sm:px-6 lg:px-8">
    <section class="max-w-7xl mx-auto mb-20">
        <div class="text-center mb-12">
            <span class="text-highlight font-bold tracking-wider text-sm uppercase mb-2 block">Pilihan Akomodasi</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900 dark:text-white">Gedung Kami</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($buildings as $building)
            <div class="group bg-card-light dark:bg-card-dark rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col h-full">
                <div class="relative h-56 overflow-hidden">
                    <div class="absolute top-4 right-4 z-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur text-xs font-bold px-3 py-1 rounded-full text-primary border border-primary/20 uppercase">
                        {{ $building->code }}
                    </div>
                    @if($building->image)
                        <img alt="{{ $building->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" src="{{ Storage::url($building->image) }}">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/80 to-primary flex items-center justify-center">
                            <span class="material-icons-round text-white/30 text-8xl">apartment</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        @php
                            $availableRooms = $building->rooms->count();
                            $badgeClass = $availableRooms > 10 ? 'bg-green-500/90' : ($availableRooms > 5 ? 'bg-highlight/90 text-black' : 'bg-red-500/90');
                        @endphp
                        <p class="text-xs font-medium {{ $badgeClass }} px-2 py-0.5 rounded text-white inline-block mb-2">
                            {{ $availableRooms }} Kamar Tersedia
                        </p>
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 font-display">{{ $building->name }}</h3>
                    <p class="text-text-muted-light dark:text-text-muted-dark text-sm mb-6 flex-grow">
                        {{ Str::limit($building->description, 120) ?: 'Akomodasi nyaman dengan fasilitas lengkap untuk kenyamanan tinggal Anda.' }}
                    </p>
                    <a class="inline-flex items-center text-primary hover:text-red-700 font-semibold text-sm group-hover:underline decoration-2 underline-offset-4" href="{{ route('building.detail', $building->code) }}">
                        Lihat Detail Gedung
                        <span class="material-icons-round text-sm ml-1 transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="max-w-7xl mx-auto py-12 px-4 rounded-3xl bg-gray-50 dark:bg-gray-800/50">
        <div class="text-center mb-16">
            <h2 class="text-2xl md:text-3xl font-display font-bold text-gray-900 dark:text-white mb-4">Mengapa Memilih Kami?</h2>
            <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="flex flex-col items-center text-center group">
                <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-6 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <span class="material-icons-round text-4xl">payments</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Harga Terjangkau</h3>
                <p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-relaxed max-w-xs">
                    Tarif khusus untuk civitas akademika UMY dengan potongan harga hingga 30% untuk long stay.
                </p>
            </div>
            <div class="flex flex-col items-center text-center group">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6 text-green-600 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                    <span class="material-icons-round text-4xl">security</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Aman & Nyaman</h3>
                <p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-relaxed max-w-xs">
                    Keamanan 24 jam dengan CCTV dan petugas keamanan, serta fasilitas lengkap untuk kenyamanan Anda.
                </p>
            </div>
            <div class="flex flex-col items-center text-center group">
                <div class="w-20 h-20 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-6 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                    <span class="material-icons-round text-4xl">place</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Lokasi Strategis</h3>
                <p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-relaxed max-w-xs">
                    Berada di dalam kampus UMY dengan akses mudah ke semua fasilitas akademik dan olahraga.
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
