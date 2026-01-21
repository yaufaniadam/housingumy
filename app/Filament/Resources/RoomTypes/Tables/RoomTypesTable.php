<?php

namespace App\Filament\Resources\RoomTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class RoomTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Tipe'),
                \Filament\Tables\Columns\IconColumn::make('is_public')
                    ->label('Sewa Harian')
                    ->boolean(),
                TextColumn::make('building.name')
                    ->searchable()
                    ->sortable()
                    ->label('Gedung'),
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Harga'),
                TextColumn::make('capacity')
                    ->sortable()
                    ->label('Kapasitas'),
                TextColumn::make('rooms_count')
                    ->counts('rooms')
                    ->label('Total Kamar'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
