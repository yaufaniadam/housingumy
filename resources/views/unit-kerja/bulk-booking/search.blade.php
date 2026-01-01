@extends('layouts.public')

@section('title', 'Booking Banyak Kamar - Unit Kerja')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('unit-kerja.dashboard') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-8 py-8">
            <h1 class="text-3xl font-bold text-white mb-2">Booking Banyak Kamar</h1>
            <p class="text-purple-100">Cari dan pilih beberapa kamar sekaligus untuk keperluan unit kerja.</p>
        </div>

        <div class="p-8">
            <form action="{{ route('unit-kerja.bulk.select') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Check-in</label>
                        <input type="date" name="check_in_date" required min="{{ now()->format('Y-m-d') }}"
                               value="{{ request('check_in_date', now()->addDay()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Check-out</label>
                        <input type="date" name="check_out_date" required 
                               value="{{ request('check_out_date', now()->addDays(2)->format('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gedung (Opsional)</label>
                    <select name="building_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Semua Gedung</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong untuk mencari di semua gedung.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-medium shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Ketersediaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
