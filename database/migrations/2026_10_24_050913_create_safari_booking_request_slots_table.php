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
        Schema::create('safari_booking_request_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safari_booking_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('park_zone_id')->constrained()->cascadeOnDelete();
            $table->date('safari_date');
            $table->boolean('was_available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safari_booking_request_slots');
    }
};
