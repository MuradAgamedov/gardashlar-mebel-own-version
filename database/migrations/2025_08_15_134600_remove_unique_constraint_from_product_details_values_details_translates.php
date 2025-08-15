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
            // Remove the unique constraint
            $table->dropUnique('pdvdt_detail_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_details_values_details_translates', function (Blueprint $table) {
            // Add back the unique constraint
            $table->unique(['product_details_values_detail_id', 'language_id'], 'pdvdt_detail_lang_unique');
        });
    }
};
