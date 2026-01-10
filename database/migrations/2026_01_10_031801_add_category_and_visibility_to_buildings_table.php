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
        Schema::table('buildings', function (Blueprint $table) {
            $table->enum('unit_category', ['public', 'partner', 'internal'])
                ->default('public')
                ->after('description');
            $table->boolean('show_in_search')->default(true)->after('unit_category');
            $table->boolean('show_pricing')->default(true)->after('show_in_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['unit_category', 'show_in_search', 'show_pricing']);
        });
    }
};
