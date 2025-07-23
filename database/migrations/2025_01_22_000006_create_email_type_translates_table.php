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
        Schema::create('email_type_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_type_id')->constrained('email_types')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of email_type and language
            $table->unique(['email_type_id', 'language_id'], 'email_type_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_type_translates');
    }
}; 