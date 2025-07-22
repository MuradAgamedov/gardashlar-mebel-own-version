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
        Schema::table('customer_rights_translates', function (Blueprint $table) {
            $table->dropForeign(['customer_rights_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('customer_rights_translates');
        
        // Create new table with only content
        Schema::create('customer_rights_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_rights_id')->constrained('customer_rights')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of customer_rights and language
            $table->unique(['customer_rights_id', 'language_id'], 'customer_rights_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_rights_translates');
    }
};
