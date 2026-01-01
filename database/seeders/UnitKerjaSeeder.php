<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Biro Kemahasiswaan',
                'code' => 'BK',
                'contact_person' => 'Ahmad Fauzi',
                'phone' => '0274-387656',
                'email' => 'kemahasiswaan@umy.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Biro Akademik',
                'code' => 'BA',
                'contact_person' => 'Sri Rahayu',
                'phone' => '0274-387657',
                'email' => 'akademik@umy.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'contact_person' => 'Dr. Bambang',
                'phone' => '0274-387658',
                'email' => 'ft@umy.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Fakultas Ekonomi dan Bisnis',
                'code' => 'FEB',
                'contact_person' => 'Dr. Siti Aminah',
                'phone' => '0274-387659',
                'email' => 'feb@umy.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Fakultas Kedokteran',
                'code' => 'FK',
                'contact_person' => 'Prof. Dr. Hendra',
                'phone' => '0274-387660',
                'email' => 'fk@umy.ac.id',
                'is_active' => true,
            ],
            [
                'name' => 'Lembaga Penelitian dan Pengabdian',
                'code' => 'LP2M',
                'contact_person' => 'Dr. Wahyu',
                'phone' => '0274-387661',
                'email' => 'lp2m@umy.ac.id',
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            UnitKerja::create($unit);
        }
    }
}
