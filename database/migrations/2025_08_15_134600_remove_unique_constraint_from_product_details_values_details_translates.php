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
        // Use raw SQL to remove the unique constraint
        DB::statement('ALTER TABLE `product_details_values_details_translates` DROP INDEX `pdvdt_detail_lang_unique`');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the unique constraint
        DB::statement('ALTER TABLE `product_details_values_details_translates` ADD UNIQUE KEY `pdvdt_detail_lang_unique` (`product_details_values_detail_id`, `language_id`)');
    }
};
