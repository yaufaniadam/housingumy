<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                'name' => 'University Resident',
                'code' => 'UR',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Asrama mahasiswa dengan fasilitas lengkap untuk kenyamanan tinggal selama masa studi.',
                'unit_category' => 'public',
                'show_in_search' => true,
                'show_pricing' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Wisma UMY',
                'code' => 'WUMY',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Wisma untuk mahasiswa pascasarjana dan kegiatan akademik, satu kompleks dengan University Resident.',
                'unit_category' => 'public',
                'show_in_search' => true,
                'show_pricing' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Professor Guest House',
                'code' => 'PGH',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Guest house premium untuk profesor tamu dan tamu VIP universitas.',
                'unit_category' => 'public',
                'show_in_search' => true,
                'show_pricing' => true,
                'is_active' => true,
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
