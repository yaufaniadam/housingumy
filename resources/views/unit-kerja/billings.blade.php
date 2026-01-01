@extends('layouts.public')

@section('title', 'Nota Tagihan - Unit Kerja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nota Tagihan</h1>
            <p class="text-gray-600">{{ $unitKerja->name }}</p>
        </div>
        <a href="{{ route('unit-kerja.dashboard') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('unit-kerja.dashboard') }}" class="bg-white text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
            Reservasi
        </a>
        <a href="{{ route('unit-kerja.billings') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium">
            Nota Tagihan
        </a>
    </div>

    <!-- Billings Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Riwayat Nota</h2>
            <p class="text-gray-500 text-sm">Daftar tagihan yang sudah diajukan ke Keuangan UMY</p>
        </div>

        @if($billings->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada nota tagihan</h3>
                <p class="text-gray-500">Nota akan dibuat setelah reservasi disetujui</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Reservasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($billings as $billing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $billing->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono font-medium text-gray-800">{{ $billing->reservation->reservation_code }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $billing->reservation->room->room_number }} - {{ $billing->reservation->room->building->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($billing->reservation->check_in_date)->format('d M') }} - 
                                    {{ \Carbon\Carbon::parse($billing->reservation->check_out_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-purple-600">
                                    Rp {{ number_format($billing->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($billing->status === 'verified')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                            Dicairkan
                                        </span>
                                    @elseif($billing->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">
                                            Proses
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
