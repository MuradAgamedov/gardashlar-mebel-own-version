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
        Schema::create('blog_post_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('description');
            $table->string('card_image_alt_text')->nullable();
            $table->longText('card_description')->nullable();
            $table->string('inner_banner_alt_text')->nullable();
            $table->string('seo_title')->nullable();
            $table->longText('seo_keywords')->nullable();
            $table->longText('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['blog_post_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_translates');
    }
};
