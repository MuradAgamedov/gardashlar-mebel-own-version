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
        Schema::create('home_page_project_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_category_id');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('project_category_id')->references('id')->on('project_categories')->onDelete('cascade');
            $table->index(['status', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_project_categories');
    }
};
