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
        // Drop translates table if it exists
        Schema::dropIfExists('company_management_translates');
        
        // Create new table with only content
        Schema::create('company_management_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_management_id')->constrained('company_managements')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of company_management and language
            $table->unique(['company_management_id', 'language_id'], 'company_management_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_management_translates');
    }
};
