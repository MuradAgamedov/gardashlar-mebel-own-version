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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Category and Product Type
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            
            // Basic Info
            $table->integer('views_count')->default(0);
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            
            // Product Details
            $table->decimal('discount_percent', 5, 2)->default(0); // 0.00 to 100.00
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
