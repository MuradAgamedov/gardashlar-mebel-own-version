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
        Schema::create('suggest_a_place_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suggest_a_place_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('second_title');
            $table->string('second_title_subtitle');
            $table->timestamps();
            
            // Add foreign key with custom name
            $table->foreign('suggest_a_place_id', 'fk_suggest_place_translate_place_id')
                  ->references('id')
                  ->on('suggest_a_places')
                  ->onDelete('cascade');
            
            // Ensure unique combination of place and language
            $table->unique(['suggest_a_place_id', 'language_id'], 'suggest_place_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggest_a_place_translates');
    }
};
