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
        Schema::create('traffic_per_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traffic_id')->references('id')->on('traffic')->onDelete('cascade');
            $table->foreignId('day_id')->references('id')->on('days')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_per_days');
    }
};
