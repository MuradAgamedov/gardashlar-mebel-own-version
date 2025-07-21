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
        Schema::create('partner_slider_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_slider_id');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
            
            // Add foreign key with custom name
            $table->foreign('partner_slider_id', 'fk_partner_slider_translate_slider_id')
                  ->references('id')
                  ->on('partner_sliders')
                  ->onDelete('cascade');
            
            // Ensure unique combination of slider and language
            $table->unique(['partner_slider_id', 'language_id'], 'partner_slider_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_slider_translates');
    }
};
