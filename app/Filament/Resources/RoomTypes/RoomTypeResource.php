<?php

namespace App\Filament\Resources\RoomTypes;

use App\Filament\Resources\RoomTypes\Pages\CreateRoomType;
use App\Filament\Resources\RoomTypes\Pages\EditRoomType;
use App\Filament\Resources\RoomTypes\Pages\ListRoomTypes;
use App\Filament\Resources\RoomTypes\Pages\ViewRoomType;
use App\Filament\Resources\RoomTypes\Schemas\RoomTypeForm;
use App\Filament\Resources\RoomTypes\Schemas\RoomTypeInfolist;
use App\Filament\Resources\RoomTypes\Tables\RoomTypesTable;
use App\Models\RoomType;
use BackedEnum;
use Filament\Resources\Resource;


use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Tipe Ruangan';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return RoomTypeForm::configure($schema);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return RoomTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoomTypes::route('/'),
            'create' => CreateRoomType::route('/create'),
            'view' => ViewRoomType::route('/{record}'),
            'edit' => EditRoomType::route('/{record}/edit'),
        ];
    }
}
