<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Room;
use App\Models\UnitKerja;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tamu')
                    ->description('Data tamu yang akan menginap')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('guest_type')
                                    ->label('Tipe Tamu')
                                    ->options([
                                        'mahasiswa' => 'Mahasiswa',
                                        'staf' => 'Staf',
                                        'dosen' => 'Dosen',
                                        'umum' => 'Umum',
                                        'unit_kerja' => 'Unit Kerja',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('unit_kerja_id', null)),
                                Select::make('unit_kerja_id')
                                    ->label('Unit Kerja')
                                    ->relationship('unitKerja', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (Get $get) => $get('guest_type') === 'unit_kerja')
                                    ->required(fn (Get $get) => $get('guest_type') === 'unit_kerja'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('guest_name')
                                    ->label('Nama Tamu')
                                    ->placeholder('Nama lengkap tamu')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('guest_identity_number')
                                    ->label('NIK/NIM/NIP')
                                    ->placeholder('Nomor identitas')
                                    ->maxLength(50),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('guest_phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->placeholder('08xxxxxxxxxx')
                                    ->maxLength(20),
                                TextInput::make('guest_email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('email@example.com')
                                    ->maxLength(100),
                            ]),
                    ]),

                Section::make('Detail Reservasi')
                    ->description('Pilih kamar dan tanggal menginap')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('room_id')
                                    ->label('Kamar')
                                    ->relationship('room', 'room_number')
                                    ->getOptionLabelFromRecordUsing(fn (Room $record) => "{$record->room_number} - {$record->building->name} ({$record->room_type})")
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        if ($state) {
                                            $room = Room::find($state);
                                            if ($room) {
                                                // Use single price for all guest types (discount via coupon in future)
                                                $set('price_per_night', $room->price);
                                                self::calculateTotal($get, $set);
                                            }
                                        }
                                    }),
                                TextInput::make('total_guests')
                                    ->label('Jumlah Tamu')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('check_in_date')
                                    ->label('Tanggal Check-in')
                                    ->minDate(now())
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotal($get, $set)),
                                DatePicker::make('check_out_date')
                                    ->label('Tanggal Check-out')
                                    ->minDate(fn (Get $get) => $get('check_in_date') ? Carbon::parse($get('check_in_date'))->addDay() : now()->addDay())
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotal($get, $set)),
                            ]),
                    ]),

                Section::make('Harga')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price_per_night')
                                    ->label('Harga per Malam')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('total_nights')
                                    ->label('Jumlah Malam')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('total_price')
                                    ->label('Total Harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),

                Section::make('Status & Catatan')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'checked_in' => 'Checked In',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                TextInput::make('reservation_code')
                                    ->label('Kode Reservasi')
                                    ->disabled()
                                    ->placeholder('Auto-generated'),
                            ]),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan untuk reservasi ini')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Isi jika reservasi ditolak')
                            ->rows(2)
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('status') === 'rejected'),
                    ]),
            ]);
    }

    private static function calculateTotal(Get $get, Set $set): void
    {
        $checkIn = $get('check_in_date');
        $checkOut = $get('check_out_date');
        $pricePerNight = $get('price_per_night') ?? 0;

        if ($checkIn && $checkOut) {
            $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
            $set('total_nights', $nights);
            $set('total_price', $nights * $pricePerNight);
        }
    }
}
