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
                        Grid::make(3)
                            ->schema([
                                TextInput::make('room_number')
                                    ->label('Kode Final (Auto)')
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                        return $rule->where('building_id', $get('building_id'));
                                    })
                                    ->required(),
                                Select::make('room_type_id')
                                    ->label('Tipe Ruangan (Kategori)')
                                    ->options(function ($get) {
                                        $buildingId = $get('building_id');
                                        if (!$buildingId) {
                                            return [];
                                        }
                                        return \App\Models\RoomType::where('building_id', $buildingId)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'available' => 'Tersedia',
                                        'occupied' => 'Terisi',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->required()
                                    ->default('available'),
                            ]),
                        
                        Section::make('Override (Opsional)')
                            ->description('Isi hanya jika berbeda dari kategori')
                            ->collapsed()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('capacity')
                                            ->label('Kapasitas (Override)')
                                            ->placeholder('Ikuti Kategori')
                                            ->numeric()
                                            ->minValue(1)
                                            ->suffix('orang'),
                                        TextInput::make('price')
                                            ->label('Tarif (Override)')
                                            ->placeholder('Ikuti Kategori')
                                            ->numeric()
                                            ->prefix('Rp'),
                                    ]),
                            ]),
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
