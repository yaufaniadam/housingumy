<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestReservations extends BaseWidget
{
    protected static ?string $heading = 'Reservasi Terbaru';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('reservation_code')
                    ->label('Kode')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->searchable(),
                TextColumn::make('room.room_number')
                    ->label('Kamar')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('check_in_date')
                    ->label('Check-in')
                    ->date('d M Y'),
                TextColumn::make('check_out_date')
                    ->label('Check-out')
                    ->date('d M Y'),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'checked_in' => 'info',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
