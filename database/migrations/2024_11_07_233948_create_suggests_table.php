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
        Schema::create('suggests', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('text');
            $table->string('phone');
            $table->string('fullName');
            $table->string('address');
            $table->foreignId("project_id")->nullable()->references('id')->on('projects')->onDelete('cascade');
            $table->foreignId("organization_id")->nullable()->references('id')->on('organizations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggests');
    }
};
