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
        // This migration is not needed - we keep language_id
        // Schema::table('product_detail_value_translates', function (Blueprint $table) {
        //     // Keep language_id as is
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};
