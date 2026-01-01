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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained();
            $table->string('type', 20); // income, expense
            $table->string('category', 100); // Sewa Kamar, Listrik, Air, Gaji, Maintenance, dll
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->foreignId('reservation_id')->nullable()->constrained(); // Link jika income dari reservasi
            $table->string('receipt_file')->nullable(); // Bukti transaksi
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
