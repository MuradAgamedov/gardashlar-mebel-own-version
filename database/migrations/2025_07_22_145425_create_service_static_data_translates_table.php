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
        Schema::create('service_static_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_static_data_id')->constrained('service_static_data')->onDelete('cascade');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('card_1_title');
            $table->string('card_1_image_alt_text')->nullable();
            $table->string('card_2_title');
            $table->string('card_2_image_alt_text')->nullable();
            $table->string('card_3_title');
            $table->string('card_3_image_alt_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_static_translates');
    }
};
