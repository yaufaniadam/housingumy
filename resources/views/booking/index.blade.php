@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Selamat Datang di Housing UMY</h1>
        <p class="text-xl text-blue-100 mb-8">Temukan akomodasi nyaman untuk kebutuhan Anda di kampus UMY</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('booking.rooms') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg">
                Cari Kamar
            </a>
            <a href="{{ route('booking.check') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                Cek Reservasi
            </a>
        </div>
    </div>
</div>

<!-- Buildings Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Gedung Kami</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($buildings as $building)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
            <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-lg text-gray-800">{{ $building->name }}</h3>
                    <span class="bg-blue-100 text-blue-600 text-sm px-2 py-1 rounded">{{ $building->code }}</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($building->description, 80) }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        <span class="font-semibold text-green-600">{{ $building->rooms->count() }}</span> kamar tersedia
                    </span>
                    <a href="{{ route('booking.rooms', ['building_id' => $building->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Lihat Kamar →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Features Section -->
<div class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Mengapa Memilih Kami?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-md text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2">Harga Terjangkau</h3>
                <p class="text-gray-600">Tarif khusus untuk civitas akademika UMY dengan potongan hingga 30%</p>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-md text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2">Aman & Nyaman</h3>
                <p class="text-gray-600">Keamanan 24 jam dengan fasilitas lengkap untuk kenyamanan Anda</p>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-md text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2">Lokasi Strategis</h3>
                <p class="text-gray-600">Berada di dalam kampus UMY dengan akses mudah ke semua fasilitas</p>
            </div>
        </div>
    </div>
</div>
@endsection
