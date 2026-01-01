@extends('layouts.public')

@section('title', 'Dashboard Unit Kerja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl p-8 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ $unitKerja->name }}</h1>
                <p class="text-purple-100 mt-1">Kode: {{ $unitKerja->code }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('unit-kerja.bulk.search') }}" 
                   class="bg-white/20 text-white px-4 py-3 rounded-lg font-semibold hover:bg-white/30 transition shadow-lg border border-white/30">
                   📚 Booking Banyak
                </a>
                <a href="{{ route('unit-kerja.rooms') }}" 
                   class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition shadow-lg">
                    + Booking Baru
                </a>
                <form action="{{ route('unit-kerja.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-3 rounded-lg transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-gray-500 text-sm">Total</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                    <p class="text-gray-500 text-sm">Pending</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['active'] }}</p>
                    <p class="text-gray-500 text-sm">Aktif</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                    <p class="text-gray-500 text-sm">Total Tagihan</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format($stats['pending_billing'], 0, ',', '.') }}</p>
                    <p class="text-gray-500 text-sm">Belum Dicairkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('unit-kerja.dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium">
            Reservasi
        </a>
        <a href="{{ route('unit-kerja.billings') }}" class="bg-white text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
            Nota Tagihan
        </a>
    </div>

    <!-- Reservations List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Reservasi</h2>
        </div>

        @if($reservations->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada reservasi</h3>
                <a href="{{ route('unit-kerja.rooms') }}" class="inline-flex items-center bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Cari Kamar
                </a>
            </div>
        @else
            <div class="divide-y">
                @foreach($reservations as $reservation)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ substr($reservation->room->room_number, 0, 3) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono font-bold text-gray-800">{{ $reservation->reservation_code }}</span>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'approved' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                'checked_in' => 'bg-blue-100 text-blue-700',
                                                'completed' => 'bg-gray-100 text-gray-700',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$reservation->status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600">Tamu: {{ $reservation->guest_name }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $reservation->room->room_number }} - {{ $reservation->room->building->name }} |
                                        {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M') }} - 
                                        {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-purple-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Harga Internal</p>
                                <a href="{{ route('unit-kerja.reservation', $reservation) }}" 
                                   class="text-sm bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300 transition mt-2 inline-block">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
