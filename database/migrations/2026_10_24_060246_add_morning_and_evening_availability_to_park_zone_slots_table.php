<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('park_zone_slots', function (Blueprint $table): void {
            $table->boolean('morning_available')->default(true);
            $table->boolean('evening_available')->default(true);
        });

        DB::table('park_zone_slots')->update([
            'morning_available' => DB::raw('is_available'),
            'evening_available' => DB::raw('is_available'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_zone_slots', function (Blueprint $table): void {
            $table->dropColumn(['morning_available', 'evening_available']);
        });
    }
};
