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
        // Create product_detail_values table
        Schema::create('product_detail_values', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->string('keyword');
            $table->timestamps();
            
            $table->index(['order']);
        });

        // Create product_detail_value_translates table
        Schema::create('product_detail_value_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_detail_value_id')->constrained('product_detail_values')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('value');
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
