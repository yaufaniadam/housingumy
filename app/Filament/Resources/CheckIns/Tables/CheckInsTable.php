<?php

namespace App\Filament\Resources\CheckIns\Tables;

use App\Models\CheckIn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CheckInsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('qr_code')
                    ->label('Kode QR')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('QR Code disalin!'),
                TextColumn::make('reservation.reservation_code')
                    ->label('Reservasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reservation.guest_name')
                    ->label('Nama Tamu')
                    ->searchable(),
                TextColumn::make('reservation.room.room_number')
                    ->label('Kamar')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('reservation.check_in_date')
                    ->label('Jadwal Check-in')
                    ->date('d M Y'),
                TextColumn::make('checked_in_at')
                    ->label('Aktual Check-in')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum check-in'),
                TextColumn::make('checked_out_at')
                    ->label('Aktual Check-out')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum check-out'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->checked_out_at) {
                            return 'Selesai';
                        } elseif ($record->checked_in_at) {
                            return 'Menginap';
                        } else {
                            return 'Menunggu';
                        }
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Menginap' => 'success',
                        'Selesai' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('belum_checkin')
                    ->label('Belum Check-in')
                    ->query(fn (Builder $query): Builder => $query->whereNull('checked_in_at')),
                Filter::make('sedang_menginap')
                    ->label('Sedang Menginap')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('checked_in_at')->whereNull('checked_out_at')),
                Filter::make('selesai')
                    ->label('Selesai')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('checked_out_at')),
            ])
            ->recordActions([
                Action::make('checkin')
                    ->label('Check-in')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Proses Check-in')
                    ->form([
                        Textarea::make('check_in_notes')
                            ->label('Catatan Check-in')
                            ->placeholder('Catatan tambahan saat check-in (opsional)'),
                    ])
                    ->visible(fn (CheckIn $record): bool => $record->checked_in_at === null)
                    ->action(function (CheckIn $record, array $data) {
                        $record->update([
                            'checked_in_at' => now(),
                            'checked_in_by' => auth()->id(),
                            'check_in_notes' => $data['check_in_notes'] ?? null,
                        ]);

                        // Update reservation status
                        $record->reservation->update(['status' => 'checked_in']);

                        Notification::make()
                            ->title('Check-in Berhasil')
                            ->body("{$record->reservation->guest_name} telah check-in ke kamar {$record->reservation->room->room_number}.")
                            ->success()
                            ->send();
                    }),

                Action::make('checkout')
                    ->label('Check-out')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Proses Check-out')
                    ->form([
                        Textarea::make('check_out_notes')
                            ->label('Catatan Check-out')
                            ->placeholder('Catatan kondisi kamar, kerusakan, dll (opsional)'),
                    ])
                    ->visible(fn (CheckIn $record): bool => $record->checked_in_at !== null && $record->checked_out_at === null)
                    ->action(function (CheckIn $record, array $data) {
                        $record->update([
                            'checked_out_at' => now(),
                            'checked_out_by' => auth()->id(),
                            'check_out_notes' => $data['check_out_notes'] ?? null,
                        ]);

                        // Update reservation status
                        $record->reservation->update(['status' => 'completed']);

                        // Release room
                        $record->reservation->room->update(['status' => 'available']);

                        Notification::make()
                            ->title('Check-out Berhasil')
                            ->body("{$record->reservation->guest_name} telah check-out dari kamar {$record->reservation->room->room_number}.")
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data check-in')
            ->emptyStateDescription('Data check-in akan muncul setelah reservasi di-approve')
            ->emptyStateIcon('heroicon-o-qr-code');
    }
}
