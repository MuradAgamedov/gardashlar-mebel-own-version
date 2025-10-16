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
        Schema::table('page_translates', function (Blueprint $table) {
            $table->string('opengraph_title')->nullable()->after('seo_description');
            $table->text('opengraph_description')->nullable()->after('opengraph_title');
            $table->string('opengraph_image_alt_text')->nullable()->after('opengraph_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_translates', function (Blueprint $table) {
            $table->dropColumn(['opengraph_title', 'opengraph_description', 'opengraph_image_alt_text']);
        });
    }
};
