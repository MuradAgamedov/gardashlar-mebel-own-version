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
        Schema::create('product_price_filters', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_price', 10, 2)->nullable(); // Minimum qiymət
            $table->decimal('max_price', 10, 2)->nullable(); // Maksimum qiymət
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('status')->default(true); // Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_filters');
    }
};
