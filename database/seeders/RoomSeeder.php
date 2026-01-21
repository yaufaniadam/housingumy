<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            // Skip Ma'had Ali as it is handled by MahadAliSeeder
            if ($building->code === 'MA' || $building->code === 'MAHAD') {
                continue;
            }

            // Get room types for this building
            $roomTypes = RoomType::where('building_id', $building->id)->get();

            if ($roomTypes->isEmpty()) {
                // Should not happen if RoomTypeSeeder ran, but safety first
                continue;
            }

            $roomCount = match ($building->code) {
                'UR' => 20,
                'WUMY' => 10,
                'PGH' => 8,
                default => 10,
            };

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = ceil($i / 5);
                
                // assign a room type round-robin or random
                $type = $roomTypes->get(($i - 1) % $roomTypes->count());

                Room::create([
                    'building_id' => $building->id,
                    'room_type_id' => $type->id,
                    'room_number' => $building->code . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'floor' => $floor,
                    'status' => 'available',
                    'is_daily_rentable' => $type->is_public, // Default to type's visibility, or just true? Seeder default: follow type?
                    // 'capacity' => null, // Intentionally null to use RoomType default
                    // 'price' => null,    // Intentionally null to use RoomType default
                    // 'description' => null,
                ]);
            }
        }
    }
}
