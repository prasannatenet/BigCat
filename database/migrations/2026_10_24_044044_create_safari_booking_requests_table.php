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
        Schema::create('safari_booking_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('national_park_id')->constrained()->cascadeOnDelete();
            $table->foreignId('park_zone_id')->constrained()->cascadeOnDelete();
            $table->string('safari_mode');
            $table->string('safari_shift');
            $table->date('safari_date');
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email');
            $table->boolean('was_available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safari_booking_requests');
    }
};
