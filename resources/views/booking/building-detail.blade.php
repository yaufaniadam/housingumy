@extends('layouts.public')

@section('title', $building->name)

@section('content')
<!-- Hero Section -->
<header class="relative bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hero-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M0 40L40 0H20L0 20M40 40V20L20 40" fill="none" stroke="white" stroke-width="2"></path>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-pattern)"></rect>
        </svg>
    </div>
    @if($building->image)
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('{{ Storage::url($building->image) }}');"></div>
    @else
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('/storage/placeholders/building-exterior.png');"></div>
    @endif
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <nav class="text-sm text-red-100 mb-6">
            <a href="{{ route('booking.index') }}" class="hover:text-white">Home</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ $building->name }}</span>
        </nav>
        <h1 class="font-display font-bold text-3xl sm:text-4xl lg:text-5xl text-white mb-4 leading-tight">
            {{ $building->name }}
        </h1>
        <div class="flex flex-wrap items-center gap-4 text-red-100">
            <div class="flex items-center gap-2">
                <span class="material-icons-round text-lg">location_on</span>
                <span>{{ $building->address ?: 'Kampus UMY, Yogyakarta' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="material-icons-round text-lg">meeting_room</span>
                <span>{{ $roomTypes->sum('available_count') }} Kamar Tersedia</span>
            </div>
            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">{{ $building->code }}</span>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Building Description -->
    <section class="mb-12">
        <h2 class="text-2xl font-display font-bold text-gray-900 dark:text-white mb-4">Tentang {{ $building->name }}</h2>
        <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed max-w-3xl">
            {{ $building->description ?: 'Akomodasi nyaman dengan fasilitas lengkap untuk kenyamanan tinggal Anda di lingkungan kampus Universitas Muhammadiyah Yogyakarta.' }}
        </p>
    </section>

    <!-- Room Types -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-display font-bold text-gray-900 dark:text-white">Tipe Kamar</h2>
            <span class="text-highlight font-bold text-sm">{{ $roomTypes->count() }} Tipe Tersedia</span>
        </div>

        @if($roomTypes->isEmpty())
            <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                <span class="material-icons-round text-6xl text-gray-300 dark:text-gray-600 mb-4">hotel</span>
                <p class="text-gray-500 dark:text-gray-400">Belum ada kamar tersedia saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($roomTypes as $roomType)
                <div class="group bg-card-light dark:bg-card-dark rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col">
                    <div class="relative h-48 overflow-hidden">
                        @if($roomType['image'])
                            <img src="{{ Storage::url($roomType['image']) }}" alt="{{ $roomType['room_type_label'] }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary/60 to-primary flex items-center justify-center">
                                <span class="material-icons-round text-white/30 text-6xl">bed</span>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-green-600">
                            {{ $roomType['available_count'] }} tersedia
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $roomType['room_type_label'] }}</h3>
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span class="flex items-center gap-1">
                                <span class="material-icons-round text-sm">person</span>
                                {{ $roomType['capacity'] }} orang
                            </span>
                        </div>
                        <p class="text-text-muted-light dark:text-text-muted-dark text-sm mb-4 flex-grow">
                            {{ Str::limit($roomType['description'], 80) ?: 'Kamar nyaman dengan fasilitas lengkap.' }}
                        </p>
                        <!-- Facilities -->
                        @if($roomType['facilities'] && $roomType['facilities']->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($roomType['facilities']->take(3) as $facility)
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded">
                                {{ $facility->name }}
                            </span>
                            @endforeach
                            @if($roomType['facilities']->count() > 3)
                                <span class="text-xs text-gray-400">+{{ $roomType['facilities']->count() - 3 }}</span>
                            @endif
                        </div>
                        @endif
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <span class="text-xl font-bold text-primary">Rp {{ number_format($roomType['price'], 0, ',', '.') }}</span>
                                <span class="text-sm text-gray-500">/malam</span>
                            </div>
                            <a href="{{ route('room.detail', [$building->code, $roomType['id']]) }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-red-800 transition-colors">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Location -->
    <section class="mb-12">
        <h2 class="text-2xl font-display font-bold text-gray-900 dark:text-white mb-6">Lokasi</h2>
        <div class="relative h-64 w-full overflow-hidden rounded-2xl bg-gray-200 dark:bg-gray-700">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex flex-col items-center">
                    <span class="material-icons-round text-primary text-5xl drop-shadow-md">location_on</span>
                    <div class="mt-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 shadow-lg text-sm font-bold text-gray-800 dark:text-white">
                        {{ $building->name }}
                    </div>
                </div>
            </div>
        </div>
        <p class="mt-4 text-text-muted-light dark:text-text-muted-dark">
            {{ $building->address ?: 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul, Yogyakarta' }}
        </p>
    </section>
</main>
@endsection
