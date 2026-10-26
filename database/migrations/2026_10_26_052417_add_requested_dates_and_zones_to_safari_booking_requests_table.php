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
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->json('requested_dates')->nullable()->after('safari_date');
            $table->json('requested_zone_ids')->nullable()->after('requested_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->dropColumn(['requested_dates', 'requested_zone_ids']);
        });
    }
};
