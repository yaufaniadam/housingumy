<?php

namespace App\Filament\Resources\FinancialTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinancialTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income' => 'Pendapatan',
                        'expense' => 'Pengeluaran',
                        default => $state,
                    }),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('building.name')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR')->label('Total')),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reservation.reservation_code')
                    ->label('Reservasi')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('receipt_file')
                    ->label('Bukti')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'income' => 'Pendapatan',
                        'expense' => 'Pengeluaran',
                    ]),
                SelectFilter::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name'),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'Sewa Kamar' => 'Sewa Kamar',
                        'Listrik' => 'Listrik',
                        'Air' => 'Air',
                        'Internet' => 'Internet',
                        'Gaji Staff' => 'Gaji Staff',
                        'Maintenance' => 'Maintenance',
                        'Kebersihan' => 'Kebersihan',
                        'Perlengkapan' => 'Perlengkapan',
                        'Lain-lain' => 'Lain-lain',
                    ]),
                Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada transaksi')
            ->emptyStateDescription('Klik tombol "Tambah Transaksi" untuk mencatat transaksi keuangan')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
