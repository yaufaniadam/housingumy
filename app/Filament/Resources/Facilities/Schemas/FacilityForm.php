<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Fasilitas')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Fasilitas')
                                    ->placeholder('Contoh: AC, Wi-Fi, TV')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->placeholder('heroicon-o-wifi')
                                    ->helperText('Gunakan format Heroicon. Contoh: heroicon-o-wifi')
                                    ->maxLength(50),
                            ]),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi singkat tentang fasilitas')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
