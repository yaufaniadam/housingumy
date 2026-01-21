<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\CheckIn;
use App\Models\Payment;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode disalin!'),
                TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            if (is_array($decoded)) {
                                return implode(', ', $decoded);
                            }
                        }
                        return $state;
                    }),
                TextColumn::make('guest_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mahasiswa' => 'info',
                        'staf' => 'primary',
                        'dosen' => 'warning',
                        'umum' => 'gray',
                        'unit_kerja' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                TextColumn::make('room.room_number')
                    ->label('Kamar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.building.name')
                    ->label('Gedung')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('check_in_date')
                    ->label('Check-in')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('check_out_date')
                    ->label('Check-out')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('total_nights')
                    ->label('Malam')
                    ->suffix(' mlm')
                    ->alignCenter(),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
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
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'checked_in' => 'Checked In',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'checked_in' => 'Checked In',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('guest_type')
                    ->label('Tipe Tamu')
                    ->options([
                        'mahasiswa' => 'Mahasiswa',
                        'staf' => 'Staf',
                        'dosen' => 'Dosen',
                        'umum' => 'Umum',
                        'unit_kerja' => 'Unit Kerja',
                    ]),
                SelectFilter::make('room.building_id')
                    ->label('Gedung')
                    ->relationship('room.building', 'name'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Reservasi')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui reservasi ini?')
                    ->visible(fn (Reservation $record): bool => $record->status === 'pending')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        // 1. Create check-in record with QR code
                        CheckIn::create([
                            'reservation_id' => $record->id,
                        ]);

                        // 2. Update room status to occupied
                        // Note: In real world, this might strict availability, but for now it's okay.
                        $record->room->update(['status' => 'occupied']);

                        // 3. For Unit Kerja: Auto-create Invoice (Payment Pending)
                        if ($record->guest_type === 'unit_kerja' && $record->unit_kerja_id) {
                            Payment::create([
                                'reservation_id' => $record->id,
                                'amount' => $record->total_price,
                                'payment_date' => now(), // Billing date
                                'payment_method' => 'internal_transfer',
                                'status' => 'pending', // Menunggu cair dari keuangan
                                'notes' => 'Tagihan Internal Generated by System',
                            ]);
                        }

                        Notification::make()
                            ->title('Reservasi Disetujui')
                            ->body("Reservasi {$record->reservation_code} berhasil diapprove" . 
                                  ($record->guest_type === 'unit_kerja' ? ' dan Invoice Internal telah dibuat.' : '.'))
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Reservasi')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Masukkan alasan penolakan reservasi'),
                    ])
                    ->visible(fn (Reservation $record): bool => $record->status === 'pending')
                    ->action(function (Reservation $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Reservasi Ditolak')
                            ->body("Reservasi {$record->reservation_code} telah ditolak.")
                            ->danger()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Reservasi')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan reservasi ini?')
                    ->visible(fn (Reservation $record): bool => in_array($record->status, ['pending', 'approved']))
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'cancelled']);

                        // Release room if it was occupied
                        if ($record->room->status === 'occupied') {
                            $record->room->update(['status' => 'available']);
                        }

                        Notification::make()
                            ->title('Reservasi Dibatalkan')
                            ->body("Reservasi {$record->reservation_code} telah dibatalkan.")
                            ->warning()
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
            ->emptyStateHeading('Belum ada reservasi')
            ->emptyStateDescription('Klik tombol "Tambah Reservasi" untuk membuat reservasi baru')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
