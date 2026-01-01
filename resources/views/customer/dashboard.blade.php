@extends('layouts.public')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Halo, {{ Auth::guard('customer')->user()->name }}! 👋</h1>
                <p class="text-blue-100 mt-1">Kelola reservasi Anda di sini</p>
            </div>
            <a href="{{ route('booking.rooms') }}" 
               class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg">
                + Booking Baru
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-gray-500 text-sm">Total Reservasi</p>
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
                    <p class="text-gray-500 text-sm">Menunggu</p>
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
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['completed'] }}</p>
                    <p class="text-gray-500 text-sm">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Reservasi Saya</h2>
        </div>

        @if($reservations->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada reservasi</h3>
                <p class="text-gray-500 mb-4">Mulai booking kamar sekarang!</p>
                <a href="{{ route('booking.rooms') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Cari Kamar
                </a>
            </div>
        @else
            <div class="divide-y">
                @foreach($reservations as $reservation)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">
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
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                'checked_in' => 'Checked-in',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Dibatalkan',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600">{{ $reservation->room->room_number }} - {{ $reservation->room->building->name }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}
                                        ({{ $reservation->total_nights }} malam)
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</p>
                                <div class="flex gap-2 mt-2">
                                    @if($reservation->status === 'approved' && !$reservation->payment)
                                        <a href="{{ route('booking.payment', $reservation) }}" 
                                           class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">
                                            Bayar
                                        </a>
                                    @endif
                                    <a href="{{ route('customer.reservation', $reservation) }}" 
                                       class="text-sm bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300 transition">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
