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
        Schema::create('product_image_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_image_id')->constrained('product_images')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('alt_text')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate translations for same image and language
            $table->unique(['product_image_id', 'language_id']);
            
            // Index for better performance
            $table->index(['product_image_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_image_translates');
    }
};
