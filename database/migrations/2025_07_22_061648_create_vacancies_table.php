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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Non-translatable title (admin input)
            $table->integer('experience_min')->default(0); // Minimum experience in years
            $table->integer('experience_max')->default(0); // Maximum experience in years
            $table->date('last_apply_date'); // Last application date
            $table->string('email'); // Contact email
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
