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
        Schema::table('all_seo_pages', function (Blueprint $table) {
            $table->boolean('rolette_actions')->default(false);
            $table->boolean('popup_ads_show')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_seo_pages', function (Blueprint $table) {
            $table->dropColumn('rolette_actions');
            $table->dropColumn('popup_ads_show');
        });
    }
};
