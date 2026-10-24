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
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('usage_limit')->nullable()->after('custom_text');
        });

        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('coupon_name')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safari_booking_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('usage_limit');
        });
    }
};
