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
        Schema::create('product_tag_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_tag_id')->constrained('product_tags')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['product_tag_id', 'language_id'], 'product_tag_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tag_translates');
    }
};
