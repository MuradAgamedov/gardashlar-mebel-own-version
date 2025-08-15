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
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('product_detail_value_translates');
        Schema::dropIfExists('product_detail_values');
        Schema::dropIfExists('product_detail_translates');
        Schema::dropIfExists('product_details');
        
        // Create new simplified product_details table
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'order']);
        });

        // Create new simplified product_detail_translates table
        Schema::create('product_detail_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_detail_id')->constrained('product_details')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('keyword');
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['product_detail_id', 'language_id'], 'product_detail_translate_unique');
            $table->index(['language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_detail_translates');
        Schema::dropIfExists('product_details');
    }
};
