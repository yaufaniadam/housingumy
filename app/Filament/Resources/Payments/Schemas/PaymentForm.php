<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Select::make('reservation_id')
                            ->label('Reservasi')
                            ->relationship('reservation', 'reservation_code')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Jumlah Bayar')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                Select::make('payment_method')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'transfer' => 'Transfer Bank',
                                        'cash' => 'Tunai',
                                        'internal_billing' => 'Internal Billing',
                                    ])
                                    ->required()
                                    ->live(),
                            ]),
                    ]),

                Section::make('Detail Transfer')
                    ->visible(fn (Get $get) => $get('payment_method') === 'transfer')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label('Nama Bank')
                                    ->placeholder('BCA, Mandiri, BNI, dll'),
                                TextInput::make('account_name')
                                    ->label('Nama Pemilik Rekening')
                                    ->placeholder('Nama di rekening'),
                                TextInput::make('account_number')
                                    ->label('Nomor Rekening')
                                    ->placeholder('1234567890'),
                            ]),
                        FileUpload::make('proof_file')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('payments')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Verifikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'verified' => 'Verified',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->live(),
                                DateTimePicker::make('paid_at')
                                    ->label('Tanggal Bayar'),
                            ]),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Isi jika pembayaran ditolak')
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
