<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-list-bullet'),
            'external' => Tab::make('Publik / External')
                ->icon('heroicon-o-users')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('unit_kerja_id'))
                ->badge(fn () => $this->getModel()::whereNull('unit_kerja_id')->where('status', 'pending')->count())
                ->badgeColor('warning'),
            'internal' => Tab::make('Internal / Unit Kerja')
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('unit_kerja_id'))
                ->badge(fn () => $this->getModel()::whereNotNull('unit_kerja_id')->where('status', 'pending')->count())
                ->badgeColor('warning'),
        ];
    }
}
