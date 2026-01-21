<?php

namespace App\Filament\Resources\RoomTypes\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoomTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Informasi Dasar')
                    ->schema([
                        TextEntry::make('building.name')->label('Gedung'),
                        TextEntry::make('name')->label('Nama Tipe'),
                        TextEntry::make('price')->money('IDR')->label('Harga'),
                        TextEntry::make('capacity')->label('Kapasitas'),
                    ])->columns(2),
                 Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('description'),
                    ]),
            ]);
    }
}
