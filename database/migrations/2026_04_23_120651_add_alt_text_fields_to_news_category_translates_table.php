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
        Schema::table('news_category_translates', function (Blueprint $table) {
            $table->string('card_image_alt_text')->nullable();
            $table->string('inner_banner_alt_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_category_translates', function (Blueprint $table) {
            $table->dropColumn('card_image_alt_text');
            $table->dropColumn('inner_banner_alt_text');
        });
    }
};
