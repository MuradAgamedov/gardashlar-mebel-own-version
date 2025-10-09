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
        Schema::table('orders', function (Blueprint $table) {
            // First, add the new column
            $table->json('product_ids')->after('id');
        });

        // Copy existing product_id values to product_ids as an array
        \DB::statement("UPDATE `orders` SET `product_ids` = JSON_ARRAY(product_id)");

        // Now we can safely remove the old column if it exists
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'product_id')) {
                $table->dropColumn('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add back the product_id column
            $table->unsignedBigInteger('product_id')->nullable()->after('id');
        });

        // Copy the first product_id from the array back to the product_id column
        \DB::statement("UPDATE `orders` SET `product_id` = JSON_UNQUOTE(JSON_EXTRACT(`product_ids`, '$[0]'))");

        // Remove the product_ids column
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('product_ids');
        });
    }
};
