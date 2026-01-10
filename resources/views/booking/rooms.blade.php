@extends('layouts.public')

@section('title', 'Cari Kamar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Cari Kamar</h2>
        <form action="{{ route('booking.rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                <input type="date" name="check_in" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="{{ now()->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                <input type="date" name="check_out" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                <select name="building_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <select name="room_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single</option>
                    <option value="double" {{ request('room_type') == 'double' ? 'selected' : '' }}>Double</option>
                    <option value="suite" {{ request('room_type') == 'suite' ? 'selected' : '' }}>Suite</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu</label>
                <input type="number" name="total_guests" value="{{ request('total_guests', 1) }}" min="1" max="10"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                    Cari
                </button>
            </div>
        </form>
    </div>


    <!-- Results -->
    <h3 class="text-xl font-bold text-gray-800 mb-4">
        @if($roomTypes->isEmpty())
            Tidak ada kamar tersedia
        @else
            {{ $roomTypes->sum('available_count') }} Kamar Tersedia dalam {{ $roomTypes->count() }} Kategori
        @endif
    </h3>
    
    @if($roomTypes->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada kamar tersedia</h4>
            <p class="text-gray-600">Coba ubah kriteria pencarian Anda</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roomTypes as $roomType)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="h-40 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center relative">
                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="absolute top-4 left-4 bg-white text-blue-600 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ ucwords(str_replace('_', ' ', $roomType['room_type'])) }}
                    </span>
                    <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        {{ $roomType['available_count'] }} Tersedia
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">{{ ucwords(str_replace('_', ' ', $roomType['room_type'])) }}</h4>
                            <p class="text-sm text-gray-500">{{ $roomType['building_name'] }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                        <span>Lantai {{ $roomType['floor'] }}</span>
                        <span>•</span>
                        <span>{{ $roomType['capacity'] }} orang</span>
                    </div>

                    @if($roomType['facilities'] && $roomType['facilities']->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($roomType['facilities']->take(3) as $facility)
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $facility->name }}</span>
                        @endforeach
                        @if($roomType['facilities']->count() > 3)
                            <span class="text-gray-400 text-xs">+{{ $roomType['facilities']->count() - 3 }} lainnya</span>
                        @endif
                    </div>
                    @endif



                    <div class="border-t pt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($roomType['price'], 0, ',', '.') }}</span>
                            <span class="text-gray-400 text-sm">/malam</span>
                        </div>
                        <a href="{{ route('booking.create', [
                                'room_type' => $roomType['room_type'], 
                                'building_id' => $roomType['building_id'],
                                'check_in' => request('check_in'), 
                                'check_out' => request('check_out'),
                                'total_guests' => request('total_guests', 1)
                            ]) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
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
