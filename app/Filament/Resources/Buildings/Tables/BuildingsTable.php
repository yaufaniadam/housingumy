<?php

namespace App\Filament\Resources\Buildings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BuildingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=B&color=7F9CF5&background=EBF4FF'),
                TextColumn::make('name')
                    ->label('Nama Gedung')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('unit_category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'partner' => 'warning',
                        'internal' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Public',
                        'partner' => 'Partner',
                        'internal' => 'Internal',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('rooms_count')
                    ->label('Jumlah Kamar')
                    ->counts('rooms')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                IconColumn::make('show_in_search')
                    ->label('Di Pencarian')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'Muncul di pencarian' : 'Tidak muncul di pencarian'),
                IconColumn::make('show_pricing')
                    ->label('Tampil Harga')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'Harga ditampilkan' : 'Harga disembunyikan'),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->address)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('unit_category')
                    ->label('Kategori')
                    ->options([
                        'public' => 'Public',
                        'partner' => 'Partner',
                        'internal' => 'Internal',
                    ]),
                TernaryFilter::make('show_in_search')
                    ->label('Tampil di Pencarian')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
                TernaryFilter::make('show_pricing')
                    ->label('Tampilkan Harga')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Non-aktif'),
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
            ->emptyStateHeading('Belum ada gedung')
            ->emptyStateDescription('Klik tombol "Tambah Gedung" untuk menambahkan gedung baru')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
