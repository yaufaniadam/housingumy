<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Models\Facility;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kamar')
                    ->description('Data dasar kamar')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('building_id')
                                    ->label('Gedung')
                                    ->relationship('building', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('room_number')
                                    ->label('Nomor Kamar')
                                    ->placeholder('Contoh: UR-001')
                                    ->required()
                                    ->maxLength(50),
                                Select::make('room_type')
                                    ->label('Tipe Kamar')
                                    ->options([
                                        'single' => 'Single',
                                        'double' => 'Double',
                                        'suite' => 'Suite',
                                    ])
                                    ->required()
                                    ->default('single'),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('floor')
                                    ->label('Lantai')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(1),
                                TextInput::make('capacity')
                                    ->label('Kapasitas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('orang')
                                    ->required()
                                    ->default(1),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'available' => 'Tersedia',
                                        'occupied' => 'Terisi',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->required()
                                    ->default('available'),
                            ]),
                    ]),

                Section::make('Tarif')
                    ->description('Harga per malam')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('price_public')
                                    ->label('Tarif Publik')
                                    ->helperText('Untuk mahasiswa, staf, dosen, dan umum')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->default(0),
                                TextInput::make('price_internal')
                                    ->label('Tarif Internal')
                                    ->helperText('Untuk Unit Kerja/Biro')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->default(0),
                            ]),
                    ]),

                Section::make('Fasilitas')
                    ->description('Pilih fasilitas yang tersedia')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        CheckboxList::make('facilities')
                            ->label('')
                            ->relationship('facilities', 'name')
                            ->columns(3)
                            ->searchable(),
                    ]),

                Section::make('Deskripsi & Media')
                    ->collapsible()
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi singkat tentang kamar')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Foto Kamar')
                            ->image()
                            ->imageEditor()
                            ->directory('rooms'),
                    ]),
            ]);
    }
}
