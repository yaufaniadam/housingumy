@extends('layouts.public')

@section('title', 'Form Reservasi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Room Summary -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ ucfirst($sampleRoom->room_type) }}</h2>
                    <p class="text-blue-100">{{ $sampleRoom->building->name }} • Lantai {{ $sampleRoom->floor }}</p>
                    <div class="bg-blue-500/30 text-white text-xs px-2 py-1 rounded inline-block mt-1">
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Nomor kamar akan ditentukan saat check-in
                    </div>
                </div>
                <div class="ml-auto text-right">
                    <div class="text-3xl font-bold">Rp {{ number_format($sampleRoom->price_public, 0, ',', '.') }}</div>
                    <div class="text-blue-100">per malam</div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form action="{{ route('booking.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="room_type" value="{{ $sampleRoom->room_type }}">
            <input type="hidden" name="building_id" value="{{ $sampleRoom->building_id }}">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3 class="text-lg font-bold text-gray-800 mb-4">Data Pemesan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK/NIM/NIP Pemesan *</label>
                    <input type="text" name="guest_identity_number" value="{{ old('guest_identity_number') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Nomor identitas pemesan">
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4">Data Tamu ({{ request('total_guests', 1) }} Orang)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @for ($i = 1; $i <= request('total_guests', 1); $i++)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tamu {{ $i }} *</label>
                        <input type="text" name="guest_names[]" value="{{ old('guest_names.' . ($i-1)) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Nama lengkap tamu {{ $i }}">
                    </div>
                @endfor
            </div>

            <!-- Hidden inputs for dates and guest count -->
            <input type="hidden" name="check_in_date" value="{{ old('check_in_date', $checkIn) }}">
            <input type="hidden" name="check_out_date" value="{{ old('check_out_date', $checkOut) }}">
            <input type="hidden" name="total_guests" value="{{ request('total_guests', 1) }}">

            <!-- Date display (read-only) -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-800 mb-2">📅 Periode Menginap</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Check-in:</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Check-out:</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" 
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Catatan tambahan untuk reservasi...">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-800 mb-2">Ringkasan</h4>
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Tipe Kamar:</span>
                        <span>{{ ucfirst($sampleRoom->room_type) }} ({{ $sampleRoom->building->name }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Harga per malam:</span>
                        <span>Rp {{ number_format($sampleRoom->price_public, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="border-t mt-2 pt-2 flex justify-between font-semibold">
                    <span>Total (dihitung setelah konfirmasi tanggal)</span>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('booking.rooms') }}" class="flex-1 bg-gray-200 text-gray-800 text-center py-3 rounded-lg hover:bg-gray-300 transition font-medium">
                    Kembali
                </a>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    Kirim Reservasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
