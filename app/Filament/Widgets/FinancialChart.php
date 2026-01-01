<?php

namespace App\Filament\Widgets;

use App\Models\FinancialTransaction;
use Filament\Widgets\ChartWidget;

class FinancialChart extends ChartWidget
{
    protected ?string $heading = 'Pendapatan vs Pengeluaran (6 Bulan Terakhir)';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect();
        $incomes = collect();
        $expenses = collect();

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));

            $income = FinancialTransaction::where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $expense = FinancialTransaction::where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $incomes->push($income);
            $expenses->push($expense);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $incomes->toArray(),
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenses->toArray(),
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
