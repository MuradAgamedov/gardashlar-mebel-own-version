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
        // Drop foreign key constraints first
        Schema::table('become_a_investor_translates', function (Blueprint $table) {
            $table->dropForeign(['become_a_investor_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('become_a_investor_translates');
        
        // Create new table with only content
        Schema::create('become_a_investor_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('become_a_investor_id')->constrained('become_a_investors')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of become_a_investor and language
            $table->unique(['become_a_investor_id', 'language_id'], 'become_investor_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('become_a_investor_translates');
    }
};
