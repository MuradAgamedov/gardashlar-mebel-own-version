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
        Schema::create('seo_page_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_page_id')->constrained('seo_pages')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('page_header')->nullable();
            $table->longText('page_footer')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->timestamps();

            $table->unique(['seo_page_id', 'language_id'], 'seo_page_translates_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_page_translates');
    }
};
