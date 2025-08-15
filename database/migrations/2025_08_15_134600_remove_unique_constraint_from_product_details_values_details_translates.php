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
        Schema::table('product_details_values_details_translates', function (Blueprint $table) {
            // First drop the foreign key constraints
            $table->dropForeign(['product_details_values_detail_id']);
            $table->dropForeign(['language_id']);
            
            // Then remove the unique constraint
            $table->dropUnique('pdvdt_detail_lang_unique');
            
            // Add back the foreign key constraints without the unique constraint
            $table->foreign('product_details_values_detail_id', 'pdvdt_detail_fk_new')
                  ->references('id')
                  ->on('product_details_values_details')
                  ->onDelete('cascade');
                  
            $table->foreign('language_id', 'pdvdt_language_fk_new')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_details_values_details_translates', function (Blueprint $table) {
            // Drop the new foreign key constraints
            $table->dropForeign(['product_details_values_detail_id']);
            $table->dropForeign(['language_id']);
            
            // Add back the unique constraint
            $table->unique(['product_details_values_detail_id', 'language_id'], 'pdvdt_detail_lang_unique');
            
            // Add back the original foreign key constraints
            $table->foreign('product_details_values_detail_id', 'pdvdt_detail_fk')
                  ->references('id')
                  ->on('product_details_values_details')
                  ->onDelete('cascade');
                  
            $table->foreign('language_id', 'pdvdt_language_fk')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }
};
