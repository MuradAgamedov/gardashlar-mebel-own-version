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
        Schema::create('excel_import_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('excel_import_id');
            $table->unsignedBigInteger('language_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('excel_import_id')->references('id')->on('excel_imports')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->unique(['excel_import_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_import_translates');
    }
};
