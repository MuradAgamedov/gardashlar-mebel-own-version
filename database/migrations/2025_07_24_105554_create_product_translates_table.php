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
        Schema::create('product_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            
            // Translatable Fields
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug');
            
            // SEO Fields
            $table->string('seo_title')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
            
            // Product Details
            $table->decimal('weight', 8, 3)->nullable(); // Weight in kg
            $table->text('material')->nullable();
            $table->text('size')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['product_id', 'language_id'], 'product_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translates');
    }
};
