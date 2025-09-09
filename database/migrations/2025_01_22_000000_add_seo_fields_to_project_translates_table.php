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
        Schema::table('project_translates', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_keywords')->nullable()->after('seo_title');
            $table->text('seo_description')->nullable()->after('seo_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_translates', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_keywords', 'seo_description']);
        });
    }
};
