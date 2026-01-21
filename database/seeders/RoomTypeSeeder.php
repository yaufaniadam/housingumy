<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Building;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. UNIRES (Code: UR)
        $unires = Building::where('code', 'UR')->first();
        if ($unires) {
            $types = [
                [
                    'name' => 'unires_reguler',
                    'description' => 'Kamar reguler untuk mahasiswa (kapasitas 2 orang). Fasilitas standar belajar.',
                    'capacity' => 2,
                    'price' => 150000, // Placeholder price
                    'is_public' => true, // By default show, allow granular control via rooms
                ],
                [
                    'name' => 'unires_double',
                    'description' => 'Kamar double dengan fasilitas lebih luas.',
                    'capacity' => 2,
                    'price' => 200000,
                    'is_public' => true,
                ],
                [
                    'name' => 'unires_suite',
                    'description' => 'Kamar suite eksklusif untuk 1 orang dengan fasilitas lengkap.',
                    'capacity' => 1,
                    'price' => 350000,
                    'is_public' => true,
                ],
            ];

            foreach ($types as $type) {
                RoomType::updateOrCreate(
                    ['building_id' => $unires->id, 'name' => $type['name']],
                    $type
                );
            }
        }

        // 2. Wisma UMY (Code: WUMY)
        $wumy = Building::where('code', 'WUMY')->first();
        if ($wumy) {
            $types = [
                [
                    'name' => 'wumy_single',
                    'description' => 'Kamar single nyaman di Wisma UMY.',
                    'capacity' => 1,
                    'price' => 250000,
                    'is_public' => true,
                ],
                [
                    'name' => 'wumy_twin',
                    'description' => 'Kamar twin bed untuk 2 orang di Wisma UMY.',
                    'capacity' => 2,
                    'price' => 300000,
                    'is_public' => true,
                ],
            ];

            foreach ($types as $type) {
                RoomType::updateOrCreate(
                    ['building_id' => $wumy->id, 'name' => $type['name']],
                    $type
                );
            }
        }
        
        // 3. PGH (Code: PGH)
        $pgh = Building::where('code', 'PGH')->first();
        if ($pgh) {
            RoomType::updateOrCreate(
                ['building_id' => $pgh->id, 'name' => 'pgh_vip'],
                [
                    'description' => 'Kamar VIP di Professor Guest House.',
                    'capacity' => 2,
                    'price' => 500000,
                    'is_public' => true,
                ]
            );
        }
    }
}
