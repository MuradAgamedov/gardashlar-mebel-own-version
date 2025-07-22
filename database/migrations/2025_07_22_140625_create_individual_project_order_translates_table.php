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
        Schema::create('individual_project_order_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('individual_project_order_id');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            
            $table->foreign('individual_project_order_id', 'ipo_order_id_foreign')
                  ->references('id')
                  ->on('individual_project_orders')
                  ->onDelete('cascade');
            
            $table->unique(['individual_project_order_id', 'language_id'], 'ipo_translate_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_project_order_translates');
    }
};
