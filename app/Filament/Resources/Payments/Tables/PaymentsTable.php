<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\FinancialTransaction;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation.reservation_code')
                    ->label('Kode Reservasi')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('reservation.guest_name')
                    ->label('Nama Tamu')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'transfer' => 'info',
                        'cash' => 'success',
                        'internal_billing' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'transfer' => 'Transfer',
                        'cash' => 'Tunai',
                        'internal_billing' => 'Internal Billing',
                        default => $state,
                    }),
                ImageColumn::make('proof_file')
                    ->label('Bukti')
                    ->circular(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('paid_at')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label('Diverifikasi')
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
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Metode')
                    ->options([
                        'transfer' => 'Transfer',
                        'cash' => 'Tunai',
                        'internal_billing' => 'Internal Billing',
                    ]),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran')
                    ->modalDescription('Apakah Anda yakin pembayaran ini valid?')
                    ->visible(fn (Payment $record): bool => $record->status === 'pending')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);

                        // Create income transaction
                        FinancialTransaction::create([
                            'building_id' => $record->reservation->room->building_id,
                            'type' => 'income',
                            'category' => 'Sewa Kamar',
                            'amount' => $record->amount,
                            'description' => "Pembayaran reservasi {$record->reservation->reservation_code}",
                            'transaction_date' => now(),
                            'reservation_id' => $record->reservation_id,
                            'created_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran Diverifikasi')
                            ->body("Pembayaran untuk {$record->reservation->reservation_code} berhasil diverifikasi.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Masukkan alasan penolakan pembayaran'),
                    ])
                    ->visible(fn (Payment $record): bool => $record->status === 'pending')
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Pembayaran Ditolak')
                            ->body("Pembayaran untuk {$record->reservation->reservation_code} telah ditolak.")
                            ->danger()
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
            ->emptyStateHeading('Belum ada pembayaran')
            ->emptyStateDescription('Pembayaran akan muncul setelah tamu melakukan pembayaran')
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
