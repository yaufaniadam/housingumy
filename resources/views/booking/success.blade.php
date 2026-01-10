@extends('layouts.public')

@section('title', 'Reservasi Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden text-center">
        <!-- Success Icon -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 py-12">
            <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <div class="p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Reservasi Berhasil Dibuat!</h1>
            <p class="text-gray-600 mb-6">Silakan selesaikan pembayaran untuk mengonfirmasi pesanan Anda</p>

            <!-- Reservation Code -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                <p class="text-sm text-blue-600 mb-2">Kode Reservasi Anda</p>
                <p class="text-3xl font-bold text-blue-700 font-mono">{{ $reservation->reservation_code }}</p>
                <p class="text-sm text-blue-600 mt-2">Gunakan kode ini untuk konfirmasi pembayaran</p>
            </div>

            <!-- Reservation Details -->
            <div class="bg-gray-50 rounded-xl p-6 text-left mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Detail Reservasi</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama Tamu:</span>
                        <span class="font-medium">
                            {{ is_array($reservation->guest_name) ? implode(', ', $reservation->guest_name) : $reservation->guest_name }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kamar:</span>
                        <span class="font-medium">{{ $reservation->room->room_number }} - {{ $reservation->room->building->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Check-in:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Check-out:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah Malam:</span>
                        <span class="font-medium">{{ $reservation->total_nights }} malam</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="text-gray-800 font-semibold">Total:</span>
                        <span class="text-blue-600 font-bold text-lg">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">Menunggu Pembayaran</span>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('booking.payment', $reservation) }}" 
                   class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Bayar Sekarang
                </a>
                <a href="{{ route('booking.index') }}" 
                   class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 transition font-medium">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
