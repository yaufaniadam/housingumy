@extends('layouts.customer')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto max-w-7xl p-6 lg:p-10 space-y-8">
    <!-- Page Heading -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                Halo, {{ explode(' ', Auth::guard('customer')->user()->name)[0] }}!
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg">Selamat datang kembali di Housing UMY.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('booking.rooms') }}" class="h-10 px-4 bg-primary text-white rounded-full flex items-center gap-2 shadow-sm border border-transparent hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="text-sm font-bold">Booking Baru</span>
            </a>
            {{-- Loyalty Points Hidden
            <div class="h-10 px-4 bg-white dark:bg-white/10 rounded-full flex items-center gap-2 shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="material-symbols-outlined text-secondary">workspace_premium</span>
                <span class="text-sm font-bold text-slate-900 dark:text-white">0 Poin</span>
            </div>
            --}}
        </div>
    </header>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left Column: Reservations (Takes 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Reservasi Saya</h2>
                 <!-- 
                <a class="text-sm font-semibold text-primary hover:text-primary/80 flex items-center gap-1" href="#">
                    Lihat Semua <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
                -->
            </div>

            @if($reservations->isEmpty())
                <div class="bg-white dark:bg-[#1a0f0e] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-12 text-center">
                    <span class="material-symbols-outlined text-[64px] text-slate-300 mb-4">calendar_month</span>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Belum ada reservasi</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Anda belum melakukan pemesanan kamar apapun saat ini.</p>
                    <a href="{{ route('booking.rooms') }}" class="inline-flex items-center justify-center bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Mulai Booking Sekarang
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($reservations as $reservation)
                        <!-- Reservation Card -->
                        <div class="bg-white dark:bg-[#1a0f0e] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden flex flex-col md:flex-row transition-shadow hover:shadow-md group">
                            <!-- Image Placeholder -->
                            <div class="w-full md:w-64 h-48 md:h-auto shrink-0 bg-slate-200 relative overflow-hidden">
                                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" 
                                     style="background-image: url('{{ $reservation->room->building->image_url ?? asset('images/building-placeholder.jpg') }}');">
                                </div>
                                <div class="absolute top-3 left-3">
                                    @php
                                        $statusClass = match($reservation->status) {
                                            'pending' => 'bg-[#F7B800] text-white',
                                            'approved' => 'bg-blue-600 text-white',
                                            'checked_in' => 'bg-[#004029] text-white', // Success Green
                                            'completed' => 'bg-slate-500 text-white',
                                            'cancelled', 'rejected' => 'bg-red-600 text-white',
                                            default => 'bg-slate-500 text-white',
                                        };
                                        $statusLabel = match($reservation->status) {
                                            'pending' => 'Menunggu Pembayaran',
                                            'approved' => 'Disetujui',
                                            'checked_in' => 'Aktif',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            'rejected' => 'Ditolak',
                                            default => ucfirst($reservation->status),
                                        };
                                        $statusIcon = match($reservation->status) {
                                            'pending' => 'schedule',
                                            'approved' => 'check_circle',
                                            'checked_in' => 'home',
                                            'completed' => 'history',
                                            'cancelled', 'rejected' => 'cancel',
                                            default => 'info',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }} shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-6 flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                                            {{ $reservation->room->building->name }}
                                        </h3>
                                        <p class="text-lg font-bold text-primary">
                                            Rp {{ number_format($reservation->total_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                                            <span>
                                                {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M') }} - 
                                                {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}
                                                ({{ $reservation->total_nights }} Malam)
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                                            <span>Kamar {{ $reservation->room->room_number }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                                            <span class="font-mono">{{ $reservation->reservation_code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                                    @if($reservation->status === 'pending')
                                        <a href="{{ route('booking.payment', $reservation) }}" class="flex-1 bg-primary hover:bg-primary/90 text-center text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition-colors shadow-sm shadow-primary/30">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('customer.reservation', $reservation) }}" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors text-center {{ $reservation->status !== 'pending' ? 'flex-1' : '' }}">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Profile Summary (Takes 1 col) -->
        <div class="lg:col-span-1 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Ringkasan Profil</h2>
            <div class="bg-white dark:bg-[#1a0f0e] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 flex flex-col items-center text-center">
                <div class="relative mb-4">
                    <div class="size-24 rounded-full bg-slate-200 bg-cover bg-center border-4 border-white dark:border-[#2d201e] shadow-md flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-[48px]">person</span>
                    </div>
                    <button class="absolute bottom-0 right-0 p-1.5 bg-primary text-white rounded-full border-2 border-white dark:border-[#2d201e] shadow-sm hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ Auth::guard('customer')->user()->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ Auth::guard('customer')->user()->email }}</p>
                
                {{-- Loyalty Section Hidden 
                <div class="w-full bg-slate-50 dark:bg-white/5 rounded-xl p-4 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-secondary">Poin Loyalitas</span>
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Silver Member</span>
                    </div>
                    <div class="flex items-end gap-1 mb-3">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                        <span class="text-sm font-medium text-slate-400 mb-1">/ 1,000</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full h-2 bg-slate-200 dark:bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-secondary rounded-full" style="width: 0%"></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 text-left">
                        Kumpulkan poin dengan melakukan transaksi booking.
                    </p>
                </div>
                --}}

                <div class="w-full grid grid-cols-2 gap-3 mt-4">
                    <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors gap-1 border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                        <span class="material-symbols-outlined text-primary">history</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Riwayat</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors gap-1 border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                        <span class="material-symbols-outlined text-primary">settings</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pengaturan</span>
                    </button>
                </div>
            </div>

            <!-- Quick Help Card -->
            <div class="bg-primary rounded-2xl shadow-lg shadow-primary/20 p-6 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-white/10">
                    <span class="material-symbols-outlined text-[120px]">support_agent</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-2">Butuh Bantuan?</h3>
                    <p class="text-white/80 text-sm mb-4">Tim support kami siap membantu masalah reservasi Anda 24/7.</p>
                    <button class="bg-white text-primary text-sm font-bold py-2 px-4 rounded-lg hover:bg-slate-100 transition-colors w-full">
                        Hubungi Admin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
