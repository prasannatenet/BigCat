<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->string('coupon_name')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->dropColumn('coupon_name');
        });
    }
};
