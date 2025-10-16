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
        Schema::create('all_seo_pages', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_indexed')->default(true);
            $table->boolean('is_followed')->default(true);
            $table->longText('page_header')->nullable();
            $table->longText('page_footer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_seo_pages');
    }
};
