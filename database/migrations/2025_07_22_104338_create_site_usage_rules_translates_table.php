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
        Schema::create('site_usage_rules_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_usage_rules_id')->constrained('site_usage_rules')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of site_usage_rules and language
            $table->unique(['site_usage_rules_id', 'language_id'], 'site_usage_rules_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_usage_rules_translates');
    }
}; 