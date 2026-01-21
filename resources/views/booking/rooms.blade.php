@extends('layouts.public')

@section('title', 'Cari Kamar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Form -->
    <div class="bg-card-light dark:bg-card-dark rounded-2xl shadow-soft p-6 lg:p-8 border border-gray-100 dark:border-gray-700 mb-12">
        <h2 class="text-xl font-display font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-primary">search</span>
            Filter Pencarian
        </h2>
        <form action="{{ route('booking.rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Check-in</label>
                <div class="relative">
                    <input type="date" name="check_in" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}" 
                           class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10" min="{{ now()->format('Y-m-d') }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-sm">calendar_today</span>
                    </div>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Check-out</label>
                <div class="relative">
                    <input type="date" name="check_out" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}" 
                           class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10">
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
                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">Tamu</label>
                <div class="relative">
                    <input type="number" name="total_guests" value="{{ request('total_guests', 1) }}" min="1" max="10"
                           class="w-full bg-background-light dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 pl-10">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-round text-gray-400 text-sm">person</span>
                    </div>
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-red-800 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors shadow-lg shadow-primary/20 flex justify-center items-center gap-2 h-[42px]">
                    <span class="material-icons-round text-sm">search</span>
                    Cari
                </button>
            </div>
        </form>
    </div>


    <!-- Results -->
    <div class="mb-8 flex items-center justify-between">
        <h3 class="text-2xl font-display font-bold text-gray-900 dark:text-white">
            @if($roomTypes->isEmpty())
                Tidak ada kamar tersedia
            @else
                {{ $roomTypes->sum('available_count') }} Kamar Tersedia
            @endif
        </h3>
        <div class="text-sm text-text-muted-light dark:text-text-muted-dark">
            Menampilkan hasil untuk tanggal yang dipilih
        </div>
    </div>
    
    @if($roomTypes->isEmpty())
        <div class="bg-card-light dark:bg-card-dark border border-gray-100 dark:border-gray-700 rounded-2xl p-12 text-center shadow-sm">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-icons-round text-4xl text-gray-400">meeting_room</span>
            </div>
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak ada kamar tersedia</h4>
            <p class="text-text-muted-light dark:text-text-muted-dark max-w-md mx-auto">
                Maaf, kami tidak menemukan kamar yang sesuai dengan kriteria pencarian Anda. Silakan coba ubah tanggal atau pilih gedung lain.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($roomTypes as $roomType)
            <div class="group bg-card-light dark:bg-card-dark rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col h-full">
                <!-- Image Section -->
                <div class="relative h-56 overflow-hidden">
                    <!-- Badges -->
                    <div class="absolute top-4 right-4 z-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur text-xs font-bold px-3 py-1 rounded-full text-primary border border-primary/20 uppercase">
                        {{ $roomType->building->name }}
                    </div>
                    
                    @if($roomType->images && count($roomType->images) > 0)
                        <img src="{{ Storage::url($roomType->images[0]) }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/80 to-primary flex items-center justify-center">
                             <span class="material-icons-round text-white/30 text-6xl">bed</span>
                        </div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    
                    <div class="absolute bottom-4 left-4 text-white">
                        <p class="text-xs font-medium bg-green-500/90 px-2 py-0.5 rounded text-white inline-block mb-2">
                            {{ $roomType->available_count }} Tersedia
                        </p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6 flex-grow flex flex-col">
                    <div class="mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white font-display leading-tight">
                                {{ ucwords(str_replace('_', ' ', $roomType->name)) }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-medium text-text-muted-light dark:text-text-muted-dark">
                            <div class="flex items-center gap-1">
                                <span class="material-icons-round text-sm">layers</span>
                                <span>Lantai {{ $roomType->rooms->first()?->floor ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-icons-round text-sm">person</span>
                                <span>{{ $roomType->capacity }} Orang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities -->
                    @if($roomType->facilities && $roomType->facilities->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($roomType->facilities->take(3) as $facility)
                            <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-md border border-gray-200 dark:border-gray-700">
                                {{ $facility->name }}
                            </span>
                        @endforeach
                        @if($roomType->facilities->count() > 3)
                            <span class="text-xs text-gray-500 flex items-center px-1">
                                +{{ $roomType->facilities->count() - 3 }} lainnya
                            </span>
                        @endif
                    </div>
                    @else
                    <p class="text-text-muted-light dark:text-text-muted-dark text-sm mb-6 line-clamp-2">
                        {{ $roomType->description ?? 'Kamar nyaman dengan fasilitas standar untuk kebutuhan istirahat Anda.' }}
                    </p>
                    @endif

                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-0.5">Harga per malam</p>
                            <span class="text-lg font-bold text-primary">Rp {{ number_format($roomType->price, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('booking.create', [
                                'room_type' => $roomType->id, 
                                'building_id' => $roomType->building_id,
                                'check_in' => request('check_in'), 
                                'check_out' => request('check_out'),
                                'total_guests' => request('total_guests', 1)
                            ]) }}" 
                           class="inline-flex items-center justify-center bg-primary hover:bg-red-800 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-lg shadow-primary/20 transform hover:-translate-y-0.5">
                            Pesan
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
