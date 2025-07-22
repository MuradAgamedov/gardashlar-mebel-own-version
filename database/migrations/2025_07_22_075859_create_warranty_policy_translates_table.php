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
        Schema::create('warranty_policy_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_policy_id')->constrained('warranty_policies')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
            
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
