@extends('layouts.public')

@section('title', 'Cari Kamar - Unit Kerja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Cari Kamar Tersedia</h1>
            <p class="text-gray-600">{{ $unitKerja->name }}</p>
        </div>
        <a href="{{ route('unit-kerja.dashboard') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form action="{{ route('unit-kerja.rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                <select name="building_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                <input type="date" name="check_in" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                <input type="date" name="check_out" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                    Cari Kamar
                </button>
            </div>
        </form>
    </div>

    <!-- Room Results -->
    @if($rooms->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak ada kamar tersedia</h3>
            <p class="text-gray-500">Coba ubah filter pencarian Anda</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rooms as $room)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-4xl font-bold text-white">{{ $room->room_number }}</span>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $room->building->name }}</h3>
                                <p class="text-gray-500 text-sm">{{ ucfirst($room->room_type) }} • Lantai {{ $room->floor }}</p>
                            </div>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">Tersedia</span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Kapasitas {{ $room->capacity }} orang</span>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-purple-600 font-medium">Harga Internal</p>
                                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($room->price_internal, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">per malam</p>
                                </div>
                                <a href="{{ route('unit-kerja.booking.create', ['room' => $room, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" 
                                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                                    Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
