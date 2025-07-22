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
        Schema::create('become_a_partner_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('become_a_partner_id')->constrained('become_a_partners')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of become_a_partner and language
            $table->unique(['become_a_partner_id', 'language_id'], 'become_partner_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('become_a_partner_translates');
    }
};
