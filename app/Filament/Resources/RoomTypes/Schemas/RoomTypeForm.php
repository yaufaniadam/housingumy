<?php

namespace App\Filament\Resources\RoomTypes\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        Select::make('building_id')
                            ->relationship('building', 'name')
                            ->required()
                            ->label('Gedung'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Tipe'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Toggle::make('is_public')
                            ->label('Tampilkan di Website (Booking Harian)')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Detail & Harga')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Harga Dasar'),
                        TextInput::make('capacity')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->label('Kapasitas (Orang)'),
                        CheckboxList::make('facilities')
                            ->relationship('facilities', 'name')
                            ->label('Fasilitas')
                            ->columns(3)
                            ->gridDirection('row')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->directory('room-types')
                            ->label('Foto Ruangan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
