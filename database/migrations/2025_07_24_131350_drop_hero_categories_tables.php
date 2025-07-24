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
        Schema::dropIfExists('hero_category_products');
        Schema::dropIfExists('hero_category_translates');
        Schema::dropIfExists('hero_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for dropping tables, so down() is empty
        // If needed, you can recreate the tables here
    }
};
