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
        // Create product_details_values_details table (non-translatable fields)
        Schema::create('product_details_values_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_detail_value_id')->constrained('product_detail_values')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['product_detail_value_id', 'order']);
        });

        // Create product_details_values_details_translates table (translatable fields)
        Schema::create('product_details_values_details_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_details_values_detail_id')->constrained('product_details_values_details')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('keyword');
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['product_details_values_detail_id', 'language_id'], 'product_details_values_detail_translate_unique');
            $table->index(['language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details_values_details_translates');
        Schema::dropIfExists('product_details_values_details');
    }
};
