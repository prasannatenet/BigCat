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
            $table->string('phone_country')->nullable()->after('phone');
            $table->string('phone_country_code', 8)->nullable()->after('phone_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->dropColumn(['phone_country', 'phone_country_code']);
        });
    }
};
