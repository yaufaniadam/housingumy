<?php

namespace App\Filament\Resources\CheckIns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CheckInForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Reservasi')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Select::make('reservation_id')
                            ->label('Reservasi')
                            ->relationship('reservation', 'reservation_code')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('qr_code')
                            ->label('Kode QR')
                            ->disabled()
                            ->placeholder('Auto-generated'),
                    ]),

                Section::make('Check-in')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('checked_in_at')
                                    ->label('Waktu Check-in'),
                                Select::make('checked_in_by')
                                    ->label('Petugas Check-in')
                                    ->relationship('checkedInBy', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Textarea::make('check_in_notes')
                            ->label('Catatan Check-in')
                            ->placeholder('Catatan saat check-in')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Check-out')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('checked_out_at')
                                    ->label('Waktu Check-out'),
                                Select::make('checked_out_by')
                                    ->label('Petugas Check-out')
                                    ->relationship('checkedOutBy', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Textarea::make('check_out_notes')
                            ->label('Catatan Check-out')
                            ->placeholder('Catatan saat check-out (kondisi kamar, dll)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
