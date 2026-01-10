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
            // Drop the internal price column
            $table->dropColumn('price_internal');
            
            // Rename price_public to price
            $table->renameColumn('price_public', 'price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Rename back to price_public
            $table->renameColumn('price', 'price_public');
            
            // Re-add price_internal column
            $table->decimal('price_internal', 12, 2)->default(0)->after('price_public');
        });
    }
};
