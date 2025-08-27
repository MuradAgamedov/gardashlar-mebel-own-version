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
        // Create product_detail_values table (non-translatable fields)
        Schema::create('product_detail_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'order']);
        });

        // Create product_detail_value_translates table (translatable fields)
        Schema::create('product_detail_value_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_detail_value_id')->constrained('product_detail_values')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('keyword');
            $table->timestamps();
            
            $table->unique(['product_detail_value_id', 'language_id'], 'product_detail_value_translate_unique');
            $table->index(['language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_detail_value_translates');
        Schema::dropIfExists('product_detail_values');
    }
};
