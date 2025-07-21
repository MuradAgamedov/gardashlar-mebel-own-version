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
        Schema::create('kiv_category_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kiv_category_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
            
            // Add foreign key with custom name
            $table->foreign('kiv_category_id', 'fk_kiv_category_translate_category_id')
                  ->references('id')
                  ->on('kiv_categories')
                  ->onDelete('cascade');
            
            // Ensure unique combination of category and language
            $table->unique(['kiv_category_id', 'language_id'], 'kiv_category_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiv_category_translates');
    }
}; 