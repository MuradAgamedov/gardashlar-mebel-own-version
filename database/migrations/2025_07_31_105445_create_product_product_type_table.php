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
        // Create pivot table
        Schema::create('product_product_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['product_id', 'product_type_id']);
        });
        
        // Remove product_type_id from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_type_id']);
            $table->dropColumn('product_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back product_type_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_type_id')->nullable()->constrained();
        });
        
        // Drop pivot table
        Schema::dropIfExists('product_product_type');
    }
};
