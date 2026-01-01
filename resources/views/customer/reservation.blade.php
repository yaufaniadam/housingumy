@extends('layouts.public')

@section('title', 'Detail Reservasi')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('customer.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
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

        <div class="p-6 space-y-6">
            @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-semibold">Alasan Penolakan:</p>
                    <p class="text-red-600">{{ $reservation->rejection_reason }}</p>
                </div>
            @endif

            <!-- Room Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Kamar</h3>
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
                    <div>
                        <span class="text-gray-500">Jumlah Malam:</span>
                        <p class="font-medium">{{ $reservation->total_nights }} malam</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Jumlah Tamu:</span>
                        <p class="font-medium">{{ $reservation->total_guests }} orang</p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="border-t pt-6">
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
                            Menunggu Verifikasi Pembayaran
                        </span>
                    @elseif($reservation->payment && $reservation->payment->status === 'verified')
                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium">
                            ✓ Lunas
                        </span>
                    @endif
                </div>
            </div>

            @if($reservation->checkIn && $reservation->checkIn->qr_code)
                <div class="bg-blue-50 rounded-lg p-6 text-center">
                    <h3 class="text-blue-800 font-semibold mb-2">QR Code Check-in</h3>
                    <p class="text-3xl font-mono font-bold text-blue-600 mb-2">{{ $reservation->checkIn->qr_code }}</p>
                    <p class="text-sm text-blue-500">Tunjukkan kode ini saat check-in di lokasi</p>
                </div>
            @endif

            @if($reservation->notes)
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Catatan</h3>
                    <p class="text-gray-600">{{ $reservation->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
