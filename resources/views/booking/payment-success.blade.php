@extends('layouts.public')

@section('title', 'Pembayaran Berhasil')

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
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Konfirmasi Pembayaran Terkirim!</h1>
            <p class="text-gray-600 mb-6">
                @if($reservation->payment->payment_method === 'transfer')
                    Bukti transfer Anda sedang diverifikasi oleh admin.
                @else
                    Silakan bayar tunai saat check-in di lokasi.
                @endif
            </p>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-xl p-6 text-left mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Detail Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kode Reservasi:</span>
                        <span class="font-mono font-bold">{{ $reservation->reservation_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode:</span>
                        <span class="font-medium">
                            {{ $reservation->payment->payment_method === 'transfer' ? 'Transfer Bank' : 'Tunai' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah:</span>
                        <span class="font-bold text-green-600">Rp {{ number_format($reservation->payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-medium">
                            Menunggu Verifikasi
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                <h4 class="font-semibold text-blue-800 mb-2">Langkah Selanjutnya:</h4>
                <ol class="text-blue-700 text-sm space-y-1 list-decimal list-inside">
                    <li>Admin akan memverifikasi pembayaran Anda (maks. 1x24 jam)</li>
                    <li>Anda akan menerima QR Code untuk check-in</li>
                    <li>Tunjukkan QR Code saat tiba di lokasi</li>
                </ol>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('booking.check', ['code' => $reservation->reservation_code]) }}" 
                   class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    Cek Status Reservasi
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
