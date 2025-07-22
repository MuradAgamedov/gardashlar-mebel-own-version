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
        Schema::create('delivery_payment_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_payment_id')->constrained('delivery_payments')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
            
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
