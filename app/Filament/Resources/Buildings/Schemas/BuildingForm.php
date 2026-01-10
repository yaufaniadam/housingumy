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

                Section::make('Kategori & Visibilitas')
                    ->description('Pengaturan kategori gedung dan tampilan di sistem')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('unit_category')
                                    ->label('Kategori Unit')
                                    ->helperText('Public: Gedung umum | Partner: Gedung mitra | Internal: Gedung internal')
                                    ->required()
                                    ->default('public')
                                    ->datalist([
                                        'public' => 'Public',
                                        'partner' => 'Partner',
                                        'internal' => 'Internal',
                                    ]),
                                Toggle::make('show_in_search')
                                    ->label('Tampil di Pencarian')
                                    ->helperText('Gedung akan muncul di hasil pencarian publik')
                                    ->default(true)
                                    ->inline(false),
                                Toggle::make('show_pricing')
                                    ->label('Tampilkan Harga')
                                    ->helperText('Harga akan ditampilkan di halaman publik')
                                    ->default(true)
                                    ->inline(false),
                            ]),
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
