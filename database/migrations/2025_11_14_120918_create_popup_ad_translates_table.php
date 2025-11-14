<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popup_ad_translates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('popup_ad_id');
            $table->string('locale', 5);

            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_alt_text')->nullable();

            $table->timestamps();

            $table->foreign('popup_ad_id')
                  ->references('id')
                  ->on('popup_ads')
                  ->onDelete('cascade');

            $table->unique(['popup_ad_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_ad_translates');
    }
};
