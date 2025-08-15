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
            $table->foreignId('product_detail_value_id')->constrained('product_detail_values', 'id', 'pdvd_product_detail_value_fk')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['product_detail_value_id', 'order'], 'pdvd_product_value_order_idx');
        });

        // Create product_details_values_details_translates table (translatable fields)
        Schema::create('product_details_values_details_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_details_values_detail_id')->constrained('product_details_values_details', 'id', 'pdvdt_detail_fk')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages', 'id', 'pdvdt_language_fk')->onDelete('cascade');
            $table->string('keyword');
            $table->text('value');
            $table->timestamps();
            
            // Removed unique constraint to allow multiple records per detail per language
            // $table->unique(['product_details_values_detail_id', 'language_id'], 'pdvdt_detail_lang_unique');
            $table->index(['language_id'], 'pdvdt_language_idx');
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
