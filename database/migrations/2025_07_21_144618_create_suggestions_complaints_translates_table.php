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
        Schema::create('suggestions_complaints_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suggestions_complaints_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('form_title');
            $table->text('form_description');
            $table->timestamps();
            $table->foreign('suggestions_complaints_id', 'fk_suggestions_complaints_translate_id')
                  ->references('id')
                  ->on('suggestions_complaints')
                  ->onDelete('cascade');
            $table->unique(['suggestions_complaints_id', 'language_id'], 'suggestions_complaints_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions_complaints_translates');
    }
}; 