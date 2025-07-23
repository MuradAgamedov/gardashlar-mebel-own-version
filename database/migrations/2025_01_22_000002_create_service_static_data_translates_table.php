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
        Schema::create('service_static_data_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_static_data_id')->constrained('service_static_data')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('card_1_image_title')->nullable();
            $table->string('card_1_image_alt_text')->nullable();
            $table->string('card_2_image_title')->nullable();
            $table->string('card_2_image_alt_text')->nullable();
            $table->string('card_3_image_title')->nullable();
            $table->string('card_3_image_alt_text')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of service_static_data and language
            $table->unique(['service_static_data_id', 'language_id'], 'service_static_data_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_static_data_translates');
    }
}; 