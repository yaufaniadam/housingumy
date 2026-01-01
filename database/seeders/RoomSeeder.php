<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();
        $facilities = Facility::all();

        foreach ($buildings as $building) {
            $roomCount = match ($building->code) {
                'UR' => 20,  // University Resident - banyak kamar
                'MA' => 15,  // Ma'had Ali
                'WP' => 10,  // Wisma Pascasarjana
                'PGH' => 8,  // Professor Guest House - lebih sedikit, lebih premium
                default => 10,
            };

            $priceMultiplier = match ($building->code) {
                'UR' => 1.0,
                'MA' => 1.0,
                'WP' => 1.5,
                'PGH' => 2.5,
                default => 1.0,
            };

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = ceil($i / 5);
                $roomType = match (true) {
                    $i % 5 === 0 => 'suite',
                    $i % 3 === 0 => 'double',
                    default => 'single',
                };

                $basePrice = match ($roomType) {
                    'single' => 150000,
                    'double' => 250000,
                    'suite' => 400000,
                };

                $room = Room::create([
                    'building_id' => $building->id,
                    'room_number' => $building->code . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'room_type' => $roomType,
                    'floor' => $floor,
                    'capacity' => match ($roomType) {
                        'single' => 1,
                        'double' => 2,
                        'suite' => 4,
                    },
                    'price_public' => $basePrice * $priceMultiplier,
                    'price_internal' => $basePrice * $priceMultiplier * 0.7, // 30% discount untuk internal
                    'status' => 'available',
                    'description' => "Kamar {$roomType} di lantai {$floor}",
                ]);

                // Attach random facilities (3-6 facilities per room)
                $randomFacilities = $facilities->random(rand(3, min(6, $facilities->count())));
                $room->facilities()->attach($randomFacilities->pluck('id'));
            }
        }
    }
}
