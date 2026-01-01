<?php

namespace App\Filament\Widgets;

use App\Models\Building;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\FinancialTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Room stats
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Reservation stats
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $activeReservations = Reservation::whereIn('status', ['approved', 'checked_in'])->count();
        $todayCheckIns = Reservation::where('check_in_date', today())->count();

        // Financial stats (this month)
        $monthlyIncome = FinancialTransaction::where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        
        $monthlyExpense = FinancialTransaction::where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        return [
            Stat::make('Kamar Tersedia', $availableRooms . ' / ' . $totalRooms)
                ->description('Tingkat okupansi: ' . $occupancyRate . '%')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color($occupancyRate > 80 ? 'success' : ($occupancyRate > 50 ? 'warning' : 'danger'))
                ->chart([45, 55, 60, 70, 65, 75, $occupancyRate]),

            Stat::make('Reservasi Pending', $pendingReservations)
                ->description('Menunggu approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingReservations > 0 ? 'warning' : 'success'),

            Stat::make('Reservasi Aktif', $activeReservations)
                ->description('Approved & Checked-in')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Check-in Hari Ini', $todayCheckIns)
                ->description(today()->format('d M Y'))
                ->descriptionIcon('heroicon-m-arrow-right-on-rectangle')
                ->color('info'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($monthlyIncome, 0, ',', '.'))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($monthlyExpense, 0, ',', '.'))
                ->description('Laba: Rp ' . number_format($monthlyIncome - $monthlyExpense, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
