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
        Schema::create('home_page_discount_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_page_discount_id')->constrained('home_page_discounts')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->string('subtitle');
            $table->string('button_link');
            $table->timestamps();
            
            $table->unique(['home_page_discount_id', 'language_id'], 'home_page_discount_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_discount_translates');
    }
};
