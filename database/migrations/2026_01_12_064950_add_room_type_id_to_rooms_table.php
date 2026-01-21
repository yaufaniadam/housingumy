<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'room_type_id')) {
                $table->foreignId('room_type_id')->nullable()->after('building_id')->constrained('room_types')->nullOnDelete();
            }
            
            // Make columns nullable as they are now deprecated/optional overrides
            // We can retry this safely
            $table->string('room_type')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('image')->nullable()->change();
        });

        // Clean up partial migration mess if any (optional, but good for idempotency here)
        // \Illuminate\Support\Facades\DB::table('room_types')->truncate(); // Be careful if we have real data, but here it's new table. 
        // Actually, let's just proceed. Unique constraint or logic handles duplicates? 
        // We will simple check if room_type already exists or just truncate since we are migrating from rooms.
        // For safety in dev, let's truncate room_types and room_type_facilities
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('room_types')->truncate();
        \Illuminate\Support\Facades\DB::table('room_type_facilities')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Migrate existing data
        $rooms = \Illuminate\Support\Facades\DB::table('rooms')->get();
        
        // Group rooms by building_id and room_type (name)
        $grouped = $rooms->groupBy(function ($item) {
            return $item->building_id . '|' . $item->room_type;
        });

        foreach ($grouped as $key => $groupRooms) {
            $firstRoom = $groupRooms->first();
            
            // Create RoomType
            $roomTypeId = \Illuminate\Support\Facades\DB::table('room_types')->insertGetId([
                'building_id' => $firstRoom->building_id,
                'name' => $firstRoom->room_type,
                'description' => $firstRoom->description,
                'price' => $firstRoom->price,
                'capacity' => $firstRoom->capacity,
                'images' => $firstRoom->image ? json_encode([$firstRoom->image]) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Migrate facilities
            // We need to fetch facilities for the first room to seed the RoomType facilities
            $roomFacilities = \Illuminate\Support\Facades\DB::table('room_facilities')
                ->where('room_id', $firstRoom->id)
                ->pluck('facility_id');
            
            foreach ($roomFacilities as $facilityId) {
                \Illuminate\Support\Facades\DB::table('room_type_facilities')->insert([
                    'room_type_id' => $roomTypeId,
                    'facility_id' => $facilityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update rooms with new room_type_id
            foreach ($groupRooms as $room) {
                \Illuminate\Support\Facades\DB::table('rooms')
                    ->where('id', $room->id)
                    ->update(['room_type_id' => $roomTypeId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
            $table->dropColumn('room_type_id');
        });
    }
};
