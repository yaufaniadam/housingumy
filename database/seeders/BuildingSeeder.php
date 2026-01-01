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
                'is_active' => true,
            ],
            [
                'name' => "Ma'had Ali",
                'code' => 'MA',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Asrama islami dengan program tahfidz dan kajian keislaman.',
                'is_active' => true,
            ],
            [
                'name' => 'Wisma Pascasarjana',
                'code' => 'WP',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Akomodasi khusus untuk mahasiswa pascasarjana dan tamu akademik.',
                'is_active' => true,
            ],
            [
                'name' => 'Professor Guest House',
                'code' => 'PGH',
                'address' => 'Kampus Terpadu UMY, Jl. Brawijaya, Kasihan, Bantul',
                'description' => 'Guest house premium untuk profesor tamu dan tamu VIP universitas.',
                'is_active' => true,
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
