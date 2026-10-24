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
        Schema::table('safari_booking_request_slots', function (Blueprint $table): void {
            $table->string('safari_shift')->default('Morning')->after('safari_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safari_booking_request_slots', function (Blueprint $table): void {
            $table->dropColumn('safari_shift');
        });
    }
};
