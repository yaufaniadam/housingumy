@extends('layouts.public')

@section('title', 'Booking Kamar - Unit Kerja')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('unit-kerja.rooms') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            ← Kembali ke Pencarian
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Room Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-8">
                <div class="h-40 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">{{ $room->room_number }}</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-800 text-xl">{{ $room->building->name }}</h3>
                    <p class="text-gray-500">{{ ucfirst($room->room_type) }} • Lantai {{ $room->floor }}</p>
                    <p class="text-gray-500 text-sm mt-2">Kapasitas: {{ $room->capacity }} orang</p>
                    
                    <div class="border-t mt-4 pt-4">
                        <p class="text-sm text-purple-600 font-medium">Harga Internal</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">per malam</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Form Reservasi</h2>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <ul class="text-red-600 text-sm list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('unit-kerja.booking.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                        <p class="text-purple-800 font-medium">Unit Kerja: {{ $unitKerja->name }}</p>
                        <p class="text-purple-600 text-sm">Pembayaran via nota internal ke Keuangan UMY</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in *</label>
                            <input type="date" name="check_in_date" value="{{ $checkIn }}" required
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-out *</label>
                            <input type="date" name="check_out_date" value="{{ $checkOut }}" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tamu *</label>
                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Nama lengkap tamu yang akan menginap">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Tamu *</label>
                            <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Tamu</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="email@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu *</label>
                        <input type="number" name="total_guests" value="{{ old('total_guests', 1) }}" required min="1" max="{{ $room->capacity }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan *</label>
                        <input type="text" name="purpose" value="{{ old('purpose') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Contoh: Kunjungan narasumber, Persiapan acara, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                  placeholder="Catatan khusus jika ada">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-medium shadow-lg">
                        Ajukan Reservasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
