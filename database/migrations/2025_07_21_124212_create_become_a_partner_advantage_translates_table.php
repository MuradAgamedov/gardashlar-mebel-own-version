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
        Schema::create('become_a_partner_advantage_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('become_a_partner_advantage_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('icon_alt_text');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            
            // Add foreign key with custom name
            $table->foreign('become_a_partner_advantage_id', 'fk_advantage_translate_advantage_id')
                  ->references('id')
                  ->on('become_a_partner_advantages')
                  ->onDelete('cascade');
            
            // Ensure unique combination of advantage and language
            $table->unique(['become_a_partner_advantage_id', 'language_id'], 'become_advantage_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('become_a_partner_advantage_translates');
    }
};
