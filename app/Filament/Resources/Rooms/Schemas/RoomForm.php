<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Models\Facility;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Ruangan')
                    ->description('Data dasar ruangan')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('building_id')
                                    ->label('Gedung')
                                    ->relationship('building', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($set, $get) {
                                        self::updateRoomNumber($set, $get);
                                    }),
                                TextInput::make('floor')
                                    ->label('Lantai')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, $get) {
                                        self::updateRoomNumber($set, $get);
                                    }),
                                TextInput::make('_room_sequence')
                                    ->label('No. Urut Kamar')
                                    ->placeholder('Contoh: 01, 11')
                                    ->helperText('Masukkan nomor urut (akan otomatis digabung dengan lantai)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->rules([
                                        function ($get, $record) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                                $buildingId = $get('building_id');
                                                $floor = $get('floor');
                                                
                                                if ($buildingId && $floor && $value) {
                                                    $building = \App\Models\Building::find($buildingId);
                                                    if ($building) {
                                                        $paddedSequence = str_pad($value, 2, '0', STR_PAD_LEFT);
                                                        $candidateCode = $building->code . '-' . $floor . $paddedSequence;
                                                        
                                                        $query = \App\Models\Room::where('room_number', $candidateCode);
                                                        
                                                        // Ignore current record if editing
                                                        if ($record) {
                                                            $query->where('id', '!=', $record->id);
                                                        }
                                                        
                                                        if ($query->exists()) {
                                                            $fail("Nomor urut {$value} sudah digunakan di lantai {$floor} gedung ini ({$candidateCode}).");
                                                        }
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->live(onBlur: true)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->room_number) {
                                            $parts = explode('-', $record->room_number);
                                            if (count($parts) > 1) {
                                                // Part 2 is "101"
                                                $fullNum = end($parts);
                                                // Remove floor prefix
                                                $floor = $record->floor;
                                                if (str_starts_with($fullNum, $floor)) {
                                                    $seq = substr($fullNum, strlen($floor));
                                                    $component->state($seq);
                                                } else {
                                                    $component->state($fullNum);
                                                }
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function ($set, $get) {
                                        self::updateRoomNumber($set, $get);
                                    })
                                    ->dehydrated(false),
                            ]),
                        Grid::make(4)
                            ->schema([
                                TextInput::make('room_number')
                                    ->label('Kode Final (Auto)')
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                        return $rule->where('building_id', $get('building_id'));
                                    })
                                    ->required()
                                    ->columnSpan(1),
                                Select::make('room_type')
                                    ->label('Tipe Ruangan')
                                    ->options([
                                        'dormitory_single' => 'Kamar Single',
                                        'dormitory_double' => 'Kamar Double',
                                        'dormitory_suite' => 'Kamar Suite',
                                        'office' => 'Kantor',
                                        'classroom' => 'Kelas',
                                    ])
                                    ->required()
                                    ->default('dormitory_single')
                                    ->columnSpan(1),
                                TextInput::make('capacity')
                                    ->label('Kapasitas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('orang')
                                    ->required()
                                    ->default(1)
                                    ->columnSpan(1),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'available' => 'Tersedia',
                                        'occupied' => 'Terisi',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->required()
                                    ->default('available')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Tarif')
                    ->description('Harga per malam')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        TextInput::make('price')
                            ->label('Tarif')
                            ->helperText('Harga standar per malam. Diskon akan diterapkan melalui kupon/admin.')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),
                    ]),

                Section::make('Fasilitas')
                    ->description('Pilih fasilitas yang tersedia')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        CheckboxList::make('facilities')
                            ->label('')
                            ->relationship('facilities', 'name')
                            ->columns(3)
                            ->searchable(),
                    ]),

                Section::make('Deskripsi & Media')
                    ->collapsible()
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi singkat tentang ruangan')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Foto Ruangan')
                            ->image()
                            ->imageEditor()
                            ->directory('rooms'),
                    ]),
            ]);
    }

    private static function updateRoomNumber($set, $get): void
    {
        $buildingId = $get('building_id');
        $floor = $get('floor');
        $sequence = $get('_room_sequence');

        if ($buildingId && $floor && $sequence) {
            $building = \App\Models\Building::find($buildingId);
            if ($building) {
                // Pad sequence to 2 digits (e.g. 1 -> 01)
                $paddedSequence = str_pad($sequence, 2, '0', STR_PAD_LEFT);
                // Combine: CODE-FLOOR+SEQUENCE (e.g. MA-101)
                $set('room_number', $building->code . '-' . $floor . $paddedSequence);
            }
        }
    }
}
