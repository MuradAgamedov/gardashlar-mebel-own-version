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
        Schema::create('product_price_filter_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_price_filter_id')->constrained('product_price_filters')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title'); // Tərcümə edilə bilən başlıq
            $table->timestamps();
            
            // Ensure unique combination of filter and language
            $table->unique(['product_price_filter_id', 'language_id'], 'price_filter_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_filter_translates');
    }
};
