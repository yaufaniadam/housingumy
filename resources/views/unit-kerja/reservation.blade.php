@extends('layouts.public')

@section('title', 'Detail Reservasi - Unit Kerja')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('unit-kerja.dashboard') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Kode Reservasi</p>
                    <p class="text-2xl font-bold font-mono">{{ $reservation->reservation_code }}</p>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-500',
                        'approved' => 'bg-green-500',
                        'rejected' => 'bg-red-500',
                        'checked_in' => 'bg-blue-500',
                        'completed' => 'bg-gray-500',
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'checked_in' => 'Sudah Check-in',
                        'completed' => 'Selesai',
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

            <!-- Guest Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Tamu</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Nama:</span>
                        <p class="font-medium">{{ $reservation->guest_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Telepon:</span>
                        <p class="font-medium">{{ $reservation->guest_phone }}</p>
                    </div>
                </div>
            </div>

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
                </div>
            </div>

            <!-- Billing Info -->
            <div class="border-t pt-6">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-500 text-sm">Total Tagihan (Harga Internal)</span>
                        <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</p>
                    </div>
                    @if($reservation->payment)
                        @if($reservation->payment->status === 'verified')
                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium">
                                ✓ Sudah Dicairkan
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium">
                                Menunggu Pencairan
                            </span>
                        @endif
                    @else
                        <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium">
                            Belum ada nota
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h4 class="font-semibold text-purple-800 mb-2">Proses Pembayaran (Internal)</h4>
                <ol class="text-purple-700 text-sm space-y-1 list-decimal list-inside">
                    <li>Reservasi disetujui oleh admin Housing</li>
                    <li>Admin Housing membuat nota tagihan</li>
                    <li>Nota diajukan ke Keuangan UMY</li>
                    <li>Keuangan UMY mencairkan ke kas Housing</li>
                </ol>
            </div>

            @if($reservation->checkIn && $reservation->checkIn->qr_code)
                <div class="bg-blue-50 rounded-lg p-6 text-center">
                    <h3 class="text-blue-800 font-semibold mb-2">QR Code Check-in</h3>
                    <p class="text-3xl font-mono font-bold text-blue-600 mb-2">{{ $reservation->checkIn->qr_code }}</p>
                    <p class="text-sm text-blue-500">Berikan kode ini kepada tamu saat check-in</p>
                </div>
            @endif

            @if($reservation->notes)
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Catatan</h3>
                    <p class="text-gray-600 whitespace-pre-line">{{ $reservation->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
