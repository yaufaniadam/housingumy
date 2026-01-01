<?php

namespace App\Filament\Resources\UnitKerjas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UnitKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Unit Kerja')
                    ->description('Data unit kerja/biro internal')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Unit Kerja')
                                    ->placeholder('Contoh: Biro Kemahasiswaan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label('Kode')
                                    ->placeholder('Contoh: BK')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                            ]),
                    ]),

                Section::make('Akses Portal')
                    ->description('Kredensial untuk login ke portal unit kerja')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn (?string $state): ?string => 
                                        filled($state) ? Hash::make($state) : null
                                    )
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->confirmed()
                                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Password')
                                    ->password()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Section::make('Kontak')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('contact_person')
                                    ->label('Person In Charge')
                                    ->placeholder('Nama PIC')
                                    ->maxLength(100),
                                TextInput::make('phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->placeholder('0274-xxxxxx')
                                    ->maxLength(20),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('unit@umy.ac.id')
                                    ->maxLength(100),
                            ]),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Alamat unit kerja')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Unit kerja yang tidak aktif tidak dapat membuat reservasi')
                            ->default(true),
                    ]),
            ]);
    }
}
