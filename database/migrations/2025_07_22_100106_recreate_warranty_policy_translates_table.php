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
        Schema::table('warranty_policy_translates', function (Blueprint $table) {
            $table->dropForeign(['warranty_policy_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('warranty_policy_translates');
        
        // Create new table with only content
        Schema::create('warranty_policy_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_policy_id')->constrained('warranty_policies')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of warranty_policy and language
            $table->unique(['warranty_policy_id', 'language_id'], 'warranty_policy_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_policy_translates');
    }
};
