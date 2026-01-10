<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if not exists
        if (!User::where('email', 'admin@housingumy.test')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@housingumy.test',
            ]);
        }

        $this->call([
            RolePermissionSeeder::class,
            BuildingSeeder::class,
            FacilitySeeder::class,
            RoomSeeder::class,
            UnitKerjaSeeder::class,
            MahadAliSeeder::class,
        ]);
    }
}
