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
        Schema::create('why_choose_us_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('why_choose_us_id')->constrained('why_choose_us')->onDelete('cascade');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            
            // First section translations
            $table->string('first_icon_alt_text')->nullable();
            $table->string('first_title')->nullable();
            $table->text('first_description')->nullable();
            
            // Second section translations
            $table->string('second_icon_alt_text')->nullable();
            $table->string('second_title')->nullable();
            $table->text('second_description')->nullable();
            
            // Third section translations
            $table->string('third_icon_alt_text')->nullable();
            $table->string('third_title')->nullable();
            $table->text('third_description')->nullable();
            
            // Fourth section translations
            $table->string('fourth_icon_alt_text')->nullable();
            $table->string('fourth_title')->nullable();
            $table->text('fourth_description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('why_choose_us_translates');
    }
};
