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
            // Skip Ma'had Ali as it is handled by MahadAliSeeder
            if ($building->code === 'MA' || $building->code === 'MAHAD') {
                continue;
            }

            $roomCount = match ($building->code) {
                'UR' => 20,  // University Resident - banyak kamar
                'WUMY' => 10,  // Wisma UMY
                'PGH' => 8,  // Professor Guest House - lebih sedikit, lebih premium
                default => 10,
            };

            $priceMultiplier = match ($building->code) {
                'UR' => 1.0,
                'WUMY' => 1.5,
                'PGH' => 2.5,
                default => 1.0,
            };

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = ceil($i / 5);
                $roomType = match (true) {
                    $i % 5 === 0 => 'dormitory_suite',
                    $i % 3 === 0 => 'dormitory_double',
                    default => 'dormitory_single',
                };

                $basePrice = match ($roomType) {
                    'dormitory_single' => 150000,
                    'dormitory_double' => 250000,
                    'dormitory_suite' => 400000,
                    default => 150000,
                };

                $room = Room::create([
                    'building_id' => $building->id,
                    'room_number' => $building->code . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'room_type' => $roomType,
                    'floor' => $floor,
                    'capacity' => match ($roomType) {
                        'dormitory_single' => 1,
                        'dormitory_double' => 2,
                        'dormitory_suite' => 4,
                    },
                    'price' => $basePrice * $priceMultiplier,
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
