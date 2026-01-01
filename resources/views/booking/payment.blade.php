@extends('layouts.public')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white p-6">
            <h1 class="text-2xl font-bold">Pembayaran Reservasi</h1>
            <p class="text-green-100">{{ $reservation->reservation_code }}</p>
        </div>

        <div class="p-6">
            <!-- Reservation Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Reservasi</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Nama:</span>
                        <p class="font-medium">{{ $reservation->guest_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Kamar:</span>
                        <p class="font-medium">{{ $reservation->room->room_number }} - {{ $reservation->room->building->name }}</p>
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
                <div class="border-t mt-4 pt-4 flex justify-between items-center">
                    <span class="font-semibold text-gray-800">Total Pembayaran:</span>
                    <span class="text-2xl font-bold text-green-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($reservation->payment && $reservation->payment->status === 'pending')
                <!-- Already paid, waiting verification -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Menunggu Verifikasi</h3>
                    <p class="text-yellow-600">Bukti pembayaran Anda sedang diproses oleh admin. Mohon tunggu maksimal 1x24 jam.</p>
                </div>
            @elseif($reservation->payment && $reservation->payment->status === 'verified')
                <!-- Payment verified -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Pembayaran Terverifikasi</h3>
                    <p class="text-green-600">Pembayaran Anda telah dikonfirmasi. Silakan datang ke lokasi sesuai jadwal check-in.</p>
                </div>
            @else
                <!-- Payment Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-800 mb-3">Instruksi Pembayaran</h3>
                    <p class="text-blue-700 mb-4">Transfer ke rekening berikut:</p>
                    <div class="bg-white rounded-lg p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Bank:</span>
                            <span class="font-semibold">Bank Mandiri</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Rekening:</span>
                            <span class="font-mono font-semibold">137-00-1234567-8</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atas Nama:</span>
                            <span class="font-semibold">Housing UMY</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-600">Jumlah:</span>
                            <span class="font-bold text-green-600">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('booking.payment.upload', $reservation) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <ul class="text-red-600 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3 class="font-semibold text-gray-800 mb-4">Konfirmasi Pembayaran</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" id="payment_method" required 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                onchange="toggleBankFields()">
                            <option value="">Pilih metode...</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai (Bayar di Lokasi)</option>
                        </select>
                    </div>

                    <div id="bank_fields" class="hidden space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank Pengirim *</label>
                                <input type="text" name="bank_name" 
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="BCA, Mandiri, BNI, dll">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening *</label>
                                <input type="text" name="account_name" 
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Nama sesuai rekening">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer *</label>
                            <input type="file" name="proof_file" accept="image/*"
                                   class="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                        </div>
                    </div>

                    <div id="cash_info" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 my-4">
                        <p class="text-yellow-800">
                            <strong>Pembayaran Tunai:</strong> Silakan bayar langsung di resepsionis saat check-in. 
                            Pastikan membawa uang pas atau kartu debit.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-medium mt-4">
                        Konfirmasi Pembayaran
                    </button>
                </form>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('booking.check', ['code' => $reservation->reservation_code]) }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Kembali ke Status Reservasi
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBankFields() {
    const method = document.getElementById('payment_method').value;
    const bankFields = document.getElementById('bank_fields');
    const cashInfo = document.getElementById('cash_info');
    
    if (method === 'transfer') {
        bankFields.classList.remove('hidden');
        cashInfo.classList.add('hidden');
    } else if (method === 'cash') {
        bankFields.classList.add('hidden');
        cashInfo.classList.remove('hidden');
    } else {
        bankFields.classList.add('hidden');
        cashInfo.classList.add('hidden');
    }
}
</script>
@endsection
