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
        Schema::create('park_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('national_park_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('default_available')->default(true);
            $table->timestamps();

            $table->unique(['national_park_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_zones');
    }
};
