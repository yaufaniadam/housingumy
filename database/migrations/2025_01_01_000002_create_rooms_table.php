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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('room_number', 50);
            $table->string('room_type', 50)->default('single'); // single, double, suite
            $table->integer('floor')->default(1);
            $table->integer('capacity')->default(1);
            $table->decimal('price_public', 12, 2)->default(0); // Tarif publik (mahasiswa, staf, dosen, umum)
            $table->decimal('price_internal', 12, 2)->default(0); // Tarif unit kerja/biro
            $table->string('status', 20)->default('available'); // available, occupied, maintenance
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['building_id', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
