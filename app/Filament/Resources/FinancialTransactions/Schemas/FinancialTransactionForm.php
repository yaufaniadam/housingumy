<?php

namespace App\Filament\Resources\FinancialTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Jenis Transaksi')
                                    ->options([
                                        'income' => 'Pendapatan',
                                        'expense' => 'Pengeluaran',
                                    ])
                                    ->required()
                                    ->live(),
                                Select::make('building_id')
                                    ->label('Gedung')
                                    ->relationship('building', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Kategori')
                                    ->options(fn (Get $get) => $get('type') === 'income' 
                                        ? [
                                            'Sewa Kamar' => 'Sewa Kamar',
                                            'Denda Keterlambatan' => 'Denda Keterlambatan',
                                            'Lain-lain' => 'Lain-lain',
                                        ]
                                        : [
                                            'Listrik' => 'Listrik',
                                            'Air' => 'Air',
                                            'Internet' => 'Internet',
                                            'Gaji Staff' => 'Gaji Staff',
                                            'Maintenance' => 'Maintenance',
                                            'Kebersihan' => 'Kebersihan',
                                            'Perlengkapan' => 'Perlengkapan',
                                            'Lain-lain' => 'Lain-lain',
                                        ])
                                    ->searchable()
                                    ->required(),
                                DatePicker::make('transaction_date')
                                    ->label('Tanggal Transaksi')
                                    ->default(now())
                                    ->required(),
                            ]),
                        TextInput::make('amount')
                            ->label('Jumlah')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ]),

                Section::make('Detail')
                    ->schema([
                        Select::make('reservation_id')
                            ->label('Terkait Reservasi')
                            ->relationship('reservation', 'reservation_code')
                            ->searchable()
                            ->preload()
                            ->helperText('Kosongkan jika tidak terkait dengan reservasi tertentu'),
                        Textarea::make('description')
                            ->label('Keterangan')
                            ->placeholder('Deskripsi transaksi')
                            ->rows(2)
                            ->columnSpanFull(),
                        FileUpload::make('receipt_file')
                            ->label('Bukti/Kwitansi')
                            ->image()
                            ->directory('financial'),
                    ]),
            ]);
    }
}
