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
        Schema::table('refund_policy_translates', function (Blueprint $table) {
            $table->dropForeign(['refund_policy_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('refund_policy_translates');
        
        // Create new table with only content
        Schema::create('refund_policy_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_policy_id')->constrained('refund_policies')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of refund_policy and language
            $table->unique(['refund_policy_id', 'language_id'], 'refund_policy_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_policy_translates');
    }
};
