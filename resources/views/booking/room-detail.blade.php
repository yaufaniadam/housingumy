@extends('layouts.public')

@section('title', $roomTypeLabel . ' - ' . $building->name)

@section('content')
<main class="flex-1 px-4 py-8 md:px-8 lg:px-12">
    <div class="mx-auto max-w-7xl">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('booking.index') }}" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('building.detail', $building->code) }}" class="hover:text-primary">{{ $building->name }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-white">{{ $roomTypeLabel }}</span>
        </nav>

        <!-- Image Gallery (Bento Grid) -->
        @php
            $images = $sampleRoom->images ?? [];
            $placeholders = [
                '/storage/placeholders/room-main.png',
                '/storage/placeholders/room-bathroom.png',
                '/storage/placeholders/room-main.png',
                '/storage/placeholders/room-bathroom.png',
            ];
            // Ensure we have at least 4 items for the grid by merging real images with placeholders
            $gallery = array_merge($images, array_slice($placeholders, count($images)));
            $gallery = array_slice($gallery, 0, 4); // Limit to 4 for layout
        @endphp
        <div class="mb-8 grid h-[300px] md:h-[450px] grid-cols-1 md:grid-cols-4 gap-2 overflow-hidden rounded-2xl">
            <!-- Main Large Image -->
            <div class="md:col-span-2 md:row-span-2 relative group cursor-pointer">
                @php 
                    $mainImage = $gallery[0]; 
                    $isUrl = str_starts_with($mainImage, 'http') || str_starts_with($mainImage, '/');
                    $mainPath = $isUrl ? $mainImage : Storage::url($mainImage);
                @endphp
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('{{ $mainPath }}');"></div>
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
            </div>
            
            <!-- Side Images -->
            @foreach(array_slice($gallery, 1) as $index => $image)
            @php 
                $isUrl = str_starts_with($image, 'http') || str_starts_with($image, '/');
                $path = $isUrl ? $image : Storage::url($image);
                $isLast = $index === 2; // Since we sliced, index 0 here is actually gallery[1]
            @endphp
            <div class="hidden md:block relative group cursor-pointer overflow-hidden">
                <div class="h-full w-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('{{ $path }}');"></div>
                @if($isLast && count($images) > 4)
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-100 transition-opacity">
                    <span class="text-white font-bold text-sm">+{{ count($images) - 4 }} Foto Lainnya</span>
                </div>
                @elseif($isLast)
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-white font-bold text-sm">Lihat Semua Foto</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 flex flex-col gap-8">
                <!-- Header & Ratings -->
                <div>
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-display font-bold text-gray-900 dark:text-white leading-tight mb-3">
                                {{ $roomTypeLabel }} - {{ $building->name }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                    <span class="material-icons-round text-primary text-lg">location_on</span>
                                    <span>{{ $building->address ?: 'Kampus UMY, Yogyakarta' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Chips/Tags -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm font-medium text-primary">
                            <span class="material-icons-round text-lg">person</span>
                            {{ $sampleRoom->capacity }} Tamu
                        </div>
                        <div class="flex items-center gap-2 rounded-full bg-green-100 dark:bg-green-900/30 px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400">
                            <span class="material-icons-round text-lg">verified</span>
                            {{ $availableCount }} Kamar Tersedia
                        </div>
                        @if($building->show_pricing)
                        <div class="flex items-center gap-2 rounded-full bg-highlight/20 px-4 py-2 text-sm font-medium text-yellow-700 dark:text-yellow-400">
                            <span class="material-icons-round text-lg">payments</span>
                            Harga Terjangkau
                        </div>
                        @endif
                    </div>
                </div>

                <div class="h-px w-full bg-gray-200 dark:bg-gray-700"></div>

                <!-- Description -->
                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tentang Kamar Ini</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $sampleRoom->description ?: 'Nikmati kenyamanan dan kemudahan di ' . $roomTypeLabel . ' ' . $building->name . '. Dirancang untuk mahasiswa, dosen tamu, dan pengunjung, kamar ini menawarkan tata letak yang luas dengan furnitur modern.' }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Nikmati lingkungan yang tenang, sempurna untuk belajar atau bersantai setelah seharian di Yogyakarta. Terletak strategis di dalam kampus UMY dengan akses mudah ke semua fasilitas akademik.
                    </p>
                </div>

                <div class="h-px w-full bg-gray-200 dark:bg-gray-700"></div>

                <!-- Amenities -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Fasilitas Kamar</h3>
                    @if($sampleRoom->facilities && $sampleRoom->facilities->count() > 0)
                    <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                        @foreach($sampleRoom->facilities as $facility)
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">{{ $facility->icon ?? 'check_circle' }}</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $facility->name }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">wifi</span>
                            <span class="text-gray-700 dark:text-gray-300">Wi-Fi Gratis</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">ac_unit</span>
                            <span class="text-gray-700 dark:text-gray-300">AC</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">shower</span>
                            <span class="text-gray-700 dark:text-gray-300">Kamar Mandi Dalam</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">desk</span>
                            <span class="text-gray-700 dark:text-gray-300">Meja Kerja</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">local_parking</span>
                            <span class="text-gray-700 dark:text-gray-300">Parkir Gratis</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons-round text-secondary text-2xl">security</span>
                            <span class="text-gray-700 dark:text-gray-300">Keamanan 24 Jam</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="h-px w-full bg-gray-200 dark:bg-gray-700"></div>

                <!-- Map -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Lokasi</h3>
                    <div class="relative h-64 w-full overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons-round text-primary text-4xl drop-shadow-md">location_on</span>
                                <div class="mt-2 rounded-lg bg-white dark:bg-gray-800 px-3 py-1 shadow-md text-xs font-bold text-gray-800 dark:text-white">
                                    {{ $building->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $building->address ?: 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul, Yogyakarta 55183' }}
                    </p>
                </div>
            </div>

            <!-- Right Column: Sticky Booking Card -->
            <div class="relative h-full">
                <div class="sticky top-24 rounded-2xl border border-gray-200 dark:border-gray-700 bg-card-light dark:bg-card-dark p-6 shadow-xl">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($sampleRoom->price, 0, ',', '.') }}</span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm"> / malam</span>
                        </div>
                        <div class="flex items-center gap-1 text-xs font-medium text-green-600 dark:text-green-400">
                            <span class="material-icons-round text-sm">check_circle</span>
                            <span>{{ $availableCount }} tersedia</span>
                        </div>
                    </div>

                    <form action="{{ route('booking.create') }}" method="GET">
                        <input type="hidden" name="room_type" value="{{ $roomTypeId }}">
                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                        
                        <div class="rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden mb-4">
                            <div class="flex border-b border-gray-300 dark:border-gray-600">
                                <div class="w-1/2 border-r border-gray-300 dark:border-gray-600 p-3">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Check-in</label>
                                    <input type="date" name="check_in" value="{{ now()->addDay()->format('Y-m-d') }}" class="w-full text-sm font-medium text-gray-900 dark:text-white mt-1 bg-transparent border-0 p-0 focus:ring-0">
                                </div>
                                <div class="w-1/2 p-3">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Check-out</label>
                                    <input type="date" name="check_out" value="{{ now()->addDays(2)->format('Y-m-d') }}" class="w-full text-sm font-medium text-gray-900 dark:text-white mt-1 bg-transparent border-0 p-0 focus:ring-0">
                                </div>
                            </div>
                            <div class="p-3">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jumlah Tamu</label>
                                <div class="flex justify-between items-center mt-1">
                                    <select name="total_guests" class="text-sm font-medium text-gray-900 dark:text-white bg-transparent border-0 p-0 focus:ring-0 w-full">
                                        @for($i = 1; $i <= $sampleRoom->capacity; $i++)
                                            <option value="{{ $i }}">{{ $i }} Tamu</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-primary py-3.5 text-center text-sm font-bold text-white hover:bg-red-800 transition-colors shadow-lg shadow-primary/30">
                            Pesan Sekarang
                        </button>
                    </form>

                    <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">Anda belum akan dikenakan biaya</p>

                    <div class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex justify-between">
                            <span>Rp {{ number_format($sampleRoom->price, 0, ',', '.') }} x 1 malam</span>
                            <span>Rp {{ number_format($sampleRoom->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="my-4 h-px w-full bg-gray-200 dark:bg-gray-700"></div>

                    <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white">
                        <span>Total</span>
                        <span>Rp {{ number_format($sampleRoom->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
