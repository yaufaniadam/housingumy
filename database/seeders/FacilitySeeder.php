<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['name' => 'AC', 'icon' => 'heroicon-o-sun', 'description' => 'Air Conditioner'],
            ['name' => 'Wi-Fi', 'icon' => 'heroicon-o-wifi', 'description' => 'Koneksi internet nirkabel'],
            ['name' => 'TV', 'icon' => 'heroicon-o-tv', 'description' => 'Televisi'],
            ['name' => 'Kulkas', 'icon' => 'heroicon-o-cube', 'description' => 'Lemari pendingin'],
            ['name' => 'Kamar Mandi Dalam', 'icon' => 'heroicon-o-home', 'description' => 'Kamar mandi pribadi di dalam kamar'],
            ['name' => 'Meja Belajar', 'icon' => 'heroicon-o-academic-cap', 'description' => 'Meja untuk belajar'],
            ['name' => 'Lemari Pakaian', 'icon' => 'heroicon-o-squares-2x2', 'description' => 'Lemari untuk menyimpan pakaian'],
            ['name' => 'Water Heater', 'icon' => 'heroicon-o-fire', 'description' => 'Pemanas air'],
            ['name' => 'Dapur Bersama', 'icon' => 'heroicon-o-cake', 'description' => 'Akses ke dapur bersama'],
            ['name' => 'Laundry', 'icon' => 'heroicon-o-sparkles', 'description' => 'Fasilitas laundry'],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}
