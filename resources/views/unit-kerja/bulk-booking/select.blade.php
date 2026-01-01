@extends('layouts.public')

@section('title', 'Pilih Kamar - Unit Kerja')

@section('content')
<form action="{{ route('unit-kerja.bulk.store') }}" method="POST" id="bookingForm">
    @csrf
    <input type="hidden" name="check_in_date" value="{{ $checkIn->format('Y-m-d') }}">
    <input type="hidden" name="check_out_date" value="{{ $checkOut->format('Y-m-d') }}">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32"> <!-- Added padding bottom for sticky footer -->
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pilih Kamar</h1>
                <p class="text-gray-600">
                    Periode: {{ $checkIn->format('d M Y') }} - {{ $checkOut->format('d M Y') }}
                    ({{ $checkIn->diffInDays($checkOut) }} Malam)
                </p>
            </div>
            <a href="{{ route('unit-kerja.bulk.search') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                Ubah Pencarian
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <ul class="text-red-600 text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($groupedRooms->isEmpty())
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <p class="text-gray-500 text-lg">Tidak ada kamar tersedia untuk periode ini.</p>
                <a href="{{ route('unit-kerja.bulk.search') }}" class="mt-4 inline-block text-purple-600 font-medium">Cari tanggal lain</a>
            </div>
        @else
            <!-- Room List -->
            <div class="space-y-8">
                @foreach($groupedRooms as $buildingName => $floors)
                    <div class="bg-white rounded-xl shadow-lg run-in overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800">{{ $buildingName }}</h2>
                        </div>
                        <div class="p-6 space-y-6">
                            @foreach($floors as $floor => $rooms)
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Lantai {{ $floor }}</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                        @foreach($rooms as $room)
                                            <label class="relative cursor-pointer group">
                                                <input type="checkbox" name="room_ids[]" value="{{ $room->id }}" 
                                                       data-price="{{ $room->price_internal }}"
                                                       class="peer sr-only room-checkbox">
                                                
                                                <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition text-center">
                                                    <div class="font-bold text-lg text-gray-800 peer-checked:text-purple-700">{{ $room->room_number }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ ucfirst($room->room_type) }}</div>
                                                    <div class="text-xs font-semibold text-purple-600 mt-2">
                                                        Rp {{ number_format($room->price_internal / 1000, 0) }}k
                                                    </div>
                                                </div>
                                                
                                                <div class="absolute top-2 right-2 hidden peer-checked:block">
                                                    <div class="bg-purple-600 text-white rounded-full p-0.5">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sticky Footer / Summary -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl p-4 sm:px-8 z-50">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full md:w-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text" name="purpose" required 
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Keperluan (Wajib diisi e.g Rapat Kerja)">
                    </div>
                    <div>
                        <input type="text" name="guest_name" 
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="Nama Tamu (Opsional)">
                    </div>
                    <div>
                        <input type="tel" name="guest_phone" 
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500"
                               placeholder="No HP Tamu (Opsional)">
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total <span id="selectedCount">0</span> Kamar</p>
                    <p class="text-xl font-bold text-purple-600" id="totalPrice">Rp 0</p>
                </div>
                <button type="submit" id="submitBtn" disabled
                        class="bg-purple-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Booking Sekarang
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.room-checkbox');
        const selectedCountEl = document.getElementById('selectedCount');
        const totalPriceEl = document.getElementById('totalPrice');
        const submitBtn = document.getElementById('submitBtn');
        const totalNights = {{ $checkIn->diffInDays($checkOut) }};

        function updateSummary() {
            let count = 0;
            let total = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    count++;
                    total += parseInt(cb.dataset.price) * totalNights;
                }
            });

            selectedCountEl.textContent = count;
            totalPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            
            submitBtn.disabled = count === 0;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });
    });
</script>
@endsection
