<?php

namespace App\Filament\Resources\Rooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=R&color=7F9CF5&background=EBF4FF'),
                TextColumn::make('room_number')
                    ->label('No. Kamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('building.name')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('room_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'gray',
                        'double' => 'info',
                        'suite' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('floor')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->suffix(' org')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('price_public')
                    ->label('Tarif Publik')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('price_internal')
                    ->label('Tarif Internal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'maintenance' => 'Maintenance',
                        default => $state,
                    }),
                TextColumn::make('facilities.name')
                    ->label('Fasilitas')
                    ->badge()
                    ->color('gray')
                    ->separator(', ')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('room_number')
            ->filters([
                SelectFilter::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('room_type')
                    ->label('Tipe Kamar')
                    ->options([
                        'single' => 'Single',
                        'double' => 'Double',
                        'suite' => 'Suite',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'maintenance' => 'Maintenance',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada kamar')
            ->emptyStateDescription('Klik tombol "Tambah Kamar" untuk menambahkan kamar baru')
            ->emptyStateIcon('heroicon-o-home-modern');
    }
}
