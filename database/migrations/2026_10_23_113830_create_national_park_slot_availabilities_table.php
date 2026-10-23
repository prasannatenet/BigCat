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
        if (! Schema::hasTable('national_park_slot_availabilities')) {
            Schema::create('national_park_slot_availabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('national_park_id')->constrained()->cascadeOnDelete();
                $table->date('slot_date');
                $table->boolean('is_available');
                $table->timestamps();

                $table->unique(['national_park_id', 'slot_date'], 'national_park_date_unique');
            });

            return;
        }

        Schema::table('national_park_slot_availabilities', function (Blueprint $table) {
            $table->unique(['national_park_id', 'slot_date'], 'national_park_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('national_park_slot_availabilities');
    }
};
