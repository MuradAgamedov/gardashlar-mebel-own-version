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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('opengraph_image')->nullable()->after('keyword');
            $table->integer('opengraph_image_width')->nullable()->after('opengraph_image');
            $table->integer('opengraph_image_height')->nullable()->after('opengraph_image_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['opengraph_image', 'opengraph_image_width', 'opengraph_image_height']);
        });
    }
};
