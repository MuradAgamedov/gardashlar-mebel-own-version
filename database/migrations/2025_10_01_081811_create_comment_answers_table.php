<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->longText('text');
            $table->timestamps();

            // Foreign key, assuming you have 'comments' table
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_answers');
    }
};
