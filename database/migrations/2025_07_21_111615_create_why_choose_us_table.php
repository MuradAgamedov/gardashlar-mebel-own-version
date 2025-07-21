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
        Schema::create('why_choose_us', function (Blueprint $table) {
            $table->id();
            $table->string('first_icon')->nullable(); // File path for first icon
            $table->string('second_icon')->nullable(); // File path for second icon
            $table->string('third_icon')->nullable(); // File path for third icon
            $table->string('fourth_icon')->nullable(); // File path for fourth icon
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('why_choose_us');
    }
};
