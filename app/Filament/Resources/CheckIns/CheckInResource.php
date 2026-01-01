<?php

namespace App\Filament\Resources\CheckIns;

use App\Filament\Resources\CheckIns\Pages\CreateCheckIn;
use App\Filament\Resources\CheckIns\Pages\EditCheckIn;
use App\Filament\Resources\CheckIns\Pages\ListCheckIns;
use App\Filament\Resources\CheckIns\Schemas\CheckInForm;
use App\Filament\Resources\CheckIns\Tables\CheckInsTable;
use App\Models\CheckIn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CheckInResource extends Resource
{
    protected static ?string $model = CheckIn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static string | \UnitEnum | null $navigationGroup = 'Reservasi';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Check-in';

    protected static ?string $pluralModelLabel = 'Check-in';

    protected static ?string $recordTitleAttribute = 'qr_code';

    public static function form(Schema $schema): Schema
    {
        return CheckInForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CheckInsTable::configure($table);
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
            'index' => ListCheckIns::route('/'),
            'create' => CreateCheckIn::route('/create'),
            'edit' => EditCheckIn::route('/{record}/edit'),
        ];
    }
}
