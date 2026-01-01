<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        UnitKerja::updateOrCreate(
            ['code' => 'BSI'],
            [
                'name' => 'Biro Sistem Informasi',
                'password' => 'password',
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'bsi@umy.ac.id',
                'address' => 'Gedung AR Fachruddin B lt. 1',
                'is_active' => true,
            ]
        );
    }
}
