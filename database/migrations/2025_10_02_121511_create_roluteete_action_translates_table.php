<?php

// database/migrations/2025_10_02_000001_create_roluteete_action_translates_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roluteete_action_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('roluteete_action_id')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

            $table->foreign('roluteete_action_id')->references('id')->on('roluteete_actions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roluteete_action_translates');
    }
};
