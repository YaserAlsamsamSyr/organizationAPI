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
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('benifit');
            $table->string('problemDate');
            $table->string('isPrevious');
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
        Schema::dropIfExists('problems');
    }
};
