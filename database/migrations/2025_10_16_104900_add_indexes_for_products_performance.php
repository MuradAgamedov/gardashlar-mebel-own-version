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
        Schema::table('products', function (Blueprint $table) {
            // Performance indexes
            $table->index(['status', 'id'], 'products_status_id_index');
            $table->index(['created_at', 'id'], 'products_created_at_id_index');
        });

        Schema::table('product_translates', function (Blueprint $table) {
            // Performance indexes for translations
            $table->index(['language_id', 'product_id'], 'product_translates_lang_product_index');
            $table->index(['title'], 'product_translates_title_index');
        });

        Schema::table('product_category_translates', function (Blueprint $table) {
            // Performance indexes for category translations
            $table->index(['language_id', 'product_category_id'], 'product_cat_translates_lang_cat_index');
        });

        Schema::table('product_type_translates', function (Blueprint $table) {
            // Performance indexes for type translations
            $table->index(['language_id', 'product_type_id'], 'product_type_translates_lang_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_id_index');
            $table->dropIndex('products_created_at_id_index');
        });

        Schema::table('product_translates', function (Blueprint $table) {
            $table->dropIndex('product_translates_lang_product_index');
            $table->dropIndex('product_translates_title_index');
        });

        Schema::table('product_category_translates', function (Blueprint $table) {
            $table->dropIndex('product_cat_translates_lang_cat_index');
        });

        Schema::table('product_type_translates', function (Blueprint $table) {
            $table->dropIndex('product_type_translates_lang_type_index');
        });
    }
};
