<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Pemasukan Cash (Customer Umum)
        // Dihitung dari reservation yang punya payment verified dan TIDAK punya unit_kerja_id
        $cashIncome = Reservation::whereNull('unit_kerja_id')
            ->whereHas('payment', function ($q) {
                $q->where('status', 'verified');
            })
            ->sum('total_price');

        // 2. Piutang Unit Kerja (Internal)
        // Dihitung dari reservation unit kerja yang statusnya completed/checked_in/approved
        // TAPI payment belum verified (belum cair dari keuangan UMY)
        $internalReceivable = Reservation::whereNotNull('unit_kerja_id')
            ->whereIn('status', ['approved', 'checked_in', 'completed'])
            ->whereDoesntHave('payment', function ($q) {
                $q->where('status', 'verified');
            })
            ->sum('total_price');

        // 3. Pemasukan Internal Cair
        // Unit kerja yang sudah verified paymentnya
        $internalIncome = Reservation::whereNotNull('unit_kerja_id')
            ->whereHas('payment', function ($q) {
                $q->where('status', 'verified');
            })
            ->sum('total_price');

        return [
            Stat::make('Pemasukan Tunai', 'Rp ' . number_format($cashIncome, 0, ',', '.'))
                ->description('Dari tamu umum via transfer')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Piutang Internal', 'Rp ' . number_format($internalReceivable, 0, ',', '.'))
                ->description('Tagihan Unit Kerja belum cair')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            Stat::make('Total Pemasukan', 'Rp ' . number_format($cashIncome + $internalIncome, 0, ',', '.'))
                ->description('Tunai + Internal Cair')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
        ];
    }
}
