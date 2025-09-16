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
        Schema::table('project_category_translates', function (Blueprint $table) {
            $table->dropColumn('seo_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_category_translates', function (Blueprint $table) {
            $table->longText('seo_content')->nullable()->after('seo_description');
        });
    }
};
