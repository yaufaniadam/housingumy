<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class MahadAliSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Ma'had Ali Unit Kerja
        $mahadAli = UnitKerja::firstOrCreate(
            ['code' => 'MAHAD_ALI'],
            [
                'name' => 'Ma\'had Ali Bin Abi Thalib',
                'password' => 'password', // Plain text as per recent fix
                'contact_person' => 'Ustadz Fulan',
                'phone' => '0274-1234567',
                'email' => 'mahad.ali@umy.ac.id',
                'address' => 'Gedung Ma\'had Ali',
                'is_active' => true,
            ]
        );

        // 2. Create a Building for Ma'had Ali (if not exists)
        $building = Building::firstOrCreate(
            ['code' => 'MAHAD'],
            [
                'name' => 'Gedung Ma\'had Ali',
                'description' => 'Gedung khusus operasional dan asrama Ma\'had Ali untuk mitra UMY',
                'address' => 'Komplek Housing UMY',
                'unit_category' => 'partner',
                'show_in_search' => false,
                'show_pricing' => false,
                'is_active' => true,
                'image' => null,
            ]
        );

        // 3. Create Rooms (Office & Classroom)
        // Office
        Room::firstOrCreate(
            ['building_id' => $building->id, 'room_number' => 'MA-101'],
            [
                'room_type' => 'office',
                'floor' => 1,
                'capacity' => 5, // Staff capacity
                'price' => 0, // Partner building, no charge
                'status' => 'available',
                'description' => 'Kantor Administrasi Ma\'had Ali',
            ]
        );

        // Classroom
        Room::firstOrCreate(
            ['building_id' => $building->id, 'room_number' => 'MA-201'],
            [
                'room_type' => 'classroom',
                'floor' => 2,
                'capacity' => 30, // Student capacity
                'price' => 0,
                'status' => 'available',
                'description' => 'Ruang Kelas A',
            ]
        );
        
        Room::firstOrCreate(
            ['building_id' => $building->id, 'room_number' => 'MA-202'],
            [
                'room_type' => 'classroom',
                'floor' => 2,
                'capacity' => 30,
                'price' => 0,
                'status' => 'available',
                'description' => 'Ruang Kelas B',
            ]
        );
    }
}
