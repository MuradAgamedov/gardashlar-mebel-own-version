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
        Schema::create('partner_sliders', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->string('image')->nullable(); // File path for image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_sliders');
    }
};
