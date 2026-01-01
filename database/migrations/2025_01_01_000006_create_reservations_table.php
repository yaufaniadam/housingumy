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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 20)->unique();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas');
            
            // Guest Information
            $table->string('guest_name');
            $table->string('guest_phone', 20)->nullable();
            $table->string('guest_email', 100)->nullable();
            $table->string('guest_identity_number', 50)->nullable(); // NIK/NIM/NIP
            $table->string('guest_type', 20)->default('umum'); // mahasiswa, staf, dosen, umum, unit_kerja
            
            // Booking Details
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('total_nights')->default(1);
            $table->integer('total_guests')->default(1);
            $table->decimal('price_per_night', 12, 2);
            $table->decimal('total_price', 12, 2);
            
            // Status & Approval
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, checked_in, completed, cancelled
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
