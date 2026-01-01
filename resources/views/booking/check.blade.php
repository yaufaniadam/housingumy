@extends('layouts.public')

@section('title', 'Cek Status Reservasi')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Cek Status Reservasi</h2>
        <form action="{{ route('booking.check') }}" method="GET" class="flex gap-4">
            <input type="text" name="code" value="{{ request('code') }}" 
                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Masukkan kode reservasi (contoh: RSV-ABC123)">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                Cari
            </button>
        </form>
    </div>

    @if(request('code'))
        @if($reservation)
            <!-- Reservation Found -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Kode Reservasi</p>
                            <p class="text-2xl font-bold font-mono">{{ $reservation->reservation_code }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-500',
                                'approved' => 'bg-green-500',
                                'rejected' => 'bg-red-500',
                                'checked_in' => 'bg-blue-500',
                                'completed' => 'bg-gray-500',
                                'cancelled' => 'bg-red-500',
                            ];
                            $statusLabels = [
                                'pending' => 'Menunggu Persetujuan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'checked_in' => 'Sudah Check-in',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="{{ $statusColors[$reservation->status] ?? 'bg-gray-500' }} px-4 py-2 rounded-full text-sm font-semibold">
                            {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <p class="text-red-800 font-semibold">Alasan Penolakan:</p>
                            <p class="text-red-600">{{ $reservation->rejection_reason }}</p>
                        </div>
                    @endif

                    <h3 class="font-semibold text-gray-800 mb-4">Detail Reservasi</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                        <div>
                            <span class="text-gray-500">Nama Tamu:</span>
                            <p class="font-medium">{{ $reservation->guest_name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Tipe:</span>
                            <p class="font-medium">{{ ucfirst($reservation->guest_type) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Telepon:</span>
                            <p class="font-medium">{{ $reservation->guest_phone }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <p class="font-medium">{{ $reservation->guest_email }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Informasi Kamar</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Kamar:</span>
                                <p class="font-medium">{{ $reservation->room->room_number }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Gedung:</span>
                                <p class="font-medium">{{ $reservation->room->building->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-in:</span>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Check-out:</span>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-500 text-sm">Total Pembayaran</span>
                                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</p>
                            </div>
                            @if($reservation->status === 'approved' && !$reservation->payment)
                                <a href="{{ route('booking.payment', $reservation) }}" 
                                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                                    Bayar Sekarang
                                </a>
                            @elseif($reservation->payment && $reservation->payment->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium">
                                    Menunggu Verifikasi
                                </span>
                            @elseif($reservation->payment && $reservation->payment->status === 'verified')
                                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium">
                                    Lunas
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($reservation->checkIn && $reservation->checkIn->qr_code)
                        <div class="mt-6 bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-blue-800 font-semibold mb-2">QR Code Check-in</p>
                            <p class="text-blue-600 font-mono text-lg">{{ $reservation->checkIn->qr_code }}</p>
                            <p class="text-sm text-blue-500 mt-2">Tunjukkan kode ini saat check-in</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Not Found -->
            <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Reservasi Tidak Ditemukan</h3>
                <p class="text-gray-600">Kode reservasi "{{ request('code') }}" tidak ditemukan. Pastikan kode yang dimasukkan sudah benar.</p>
            </div>
        @endif
    @endif
</div>
@endsection
