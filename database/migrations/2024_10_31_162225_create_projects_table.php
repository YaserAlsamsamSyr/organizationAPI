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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('address');
            $table->string('logo')->default('no logo');
            $table->date('start_At');
            $table->date('end_At');
            $table->float('rate')->default(0);
            $table->integer('benefitDir');
            $table->integer('benefitUnd');
            $table->string('pdfURL')->default('no pdf');
            $table->string('videoURL')->default('no video');
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
