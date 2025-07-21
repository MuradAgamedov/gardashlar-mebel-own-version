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
        Schema::create('certificate_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certificate_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('image_alt_text');
            $table->string('title');
            $table->timestamps();
            
            // Add foreign key with custom name
            $table->foreign('certificate_id', 'fk_certificate_translate_certificate_id')
                  ->references('id')
                  ->on('certificates')
                  ->onDelete('cascade');
            
            // Ensure unique combination of certificate and language
            $table->unique(['certificate_id', 'language_id'], 'certificate_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_translates');
    }
}; 