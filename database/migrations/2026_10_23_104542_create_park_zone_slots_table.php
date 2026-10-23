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
        Schema::create('park_zone_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_zone_id')->constrained()->cascadeOnDelete();
            $table->date('slot_date');
            $table->boolean('is_available');
            $table->timestamps();

            $table->unique(['park_zone_id', 'slot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_zone_slots');
    }
};
