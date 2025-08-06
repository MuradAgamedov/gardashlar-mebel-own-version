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
        Schema::create('vacancy_requirement_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_requirement_id')->constrained('vacancy_requirements')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate translations
            $table->unique(['vacancy_requirement_id', 'language_id'], 'unique_vacancy_requirement_translation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancy_requirement_translates');
    }
};
