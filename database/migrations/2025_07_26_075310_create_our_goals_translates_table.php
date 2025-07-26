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
        Schema::create('our_goals_translates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_goal_id')->constrained('our_goals')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            
            $table->unique(['our_goal_id', 'language_id'], 'our_goals_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_goals_translates');
    }
};
