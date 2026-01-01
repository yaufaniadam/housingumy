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
                    <h2 class="text-2xl font-bold">{{ $room->room_number }}</h2>
                    <p class="text-blue-100">{{ $room->building->name }} • {{ ucfirst($room->room_type) }} • Lantai {{ $room->floor }}</p>
                </div>
                <div class="ml-auto text-right">
                    <div class="text-3xl font-bold">Rp {{ number_format($room->price_public, 0, ',', '.') }}</div>
                    <div class="text-blue-100">per malam</div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form action="{{ route('booking.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3 class="text-lg font-bold text-gray-800 mb-4">Data Tamu</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Masukkan nama lengkap">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Tamu *</label>
                    <select name="guest_type" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih tipe...</option>
                        <option value="mahasiswa" {{ old('guest_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('guest_type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="staf" {{ old('guest_type') == 'staf' ? 'selected' : '' }}>Staf</option>
                        <option value="umum" {{ old('guest_type') == 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK/NIM/NIP *</label>
                    <input type="text" name="guest_identity_number" value="{{ old('guest_identity_number') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Nomor identitas">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu *</label>
                    <input type="number" name="total_guests" value="{{ old('total_guests', 1) }}" min="1" max="{{ $room->capacity }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon *</label>
                    <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="email@example.com">
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4">Tanggal Menginap</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-in *</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date', $checkIn) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out *</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date', $checkOut) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <span>Kamar:</span>
                        <span>{{ $room->room_number }} ({{ $room->building->name }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Harga per malam:</span>
                        <span>Rp {{ number_format($room->price_public, 0, ',', '.') }}</span>
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
