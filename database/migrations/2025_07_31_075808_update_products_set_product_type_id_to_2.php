<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if product_type_id = 2 exists, if not create it
        $productType = DB::table('product_types')->where('id', 2)->first();
        if (!$productType) {
            DB::table('product_types')->insert([
                'id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Update all existing products to have product_type_id = 2
        DB::table('products')->update(['product_type_id' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We cannot restore the original values since we don't know what they were
        // This is a one-way migration
    }
};
