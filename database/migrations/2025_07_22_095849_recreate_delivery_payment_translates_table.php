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
        Schema::table('delivery_payment_translates', function (Blueprint $table) {
            $table->dropForeign(['delivery_payment_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('delivery_payment_translates');
        
        // Create new table with only content
        Schema::create('delivery_payment_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_payment_id')->constrained('delivery_payments')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            
            // Ensure unique combination of delivery_payment and language
            $table->unique(['delivery_payment_id', 'language_id'], 'delivery_payment_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_payment_translates');
    }
};
