<?php

namespace App\Filament\Resources\Buildings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Gedung')
                    ->description('Data dasar gedung')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Gedung')
                                    ->placeholder('Contoh: University Resident')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label('Kode')
                                    ->placeholder('Contoh: UR')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                            ]),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Masukkan alamat lengkap gedung')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi singkat tentang gedung')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Media & Status')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Foto Gedung')
                            ->image()
                            ->imageEditor()
                            ->directory('buildings')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Gedung yang tidak aktif tidak akan ditampilkan untuk reservasi')
                            ->default(true),
                    ]),
            ]);
    }
}
