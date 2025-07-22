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
            $table->longText('description'); // For CKEditor content
            $table->timestamps();
            
            // Ensure unique combination of requirement and language
            $table->unique(['vacancy_requirement_id', 'language_id']);
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
