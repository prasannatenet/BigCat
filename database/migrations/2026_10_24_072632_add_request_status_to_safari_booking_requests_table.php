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
            $table->string('request_status')->default('pending')->after('was_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->dropColumn('request_status');
        });
    }
};
