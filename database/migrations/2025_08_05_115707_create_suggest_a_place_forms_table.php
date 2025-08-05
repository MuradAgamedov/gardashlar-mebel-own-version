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
        Schema::create('suggest_a_place_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name_surname'); // Ad, Soyad
            $table->string('email'); // Email
            $table->string('area_sqm'); // Sahə kv²
            $table->string('mobile'); // Mobile
            $table->string('address'); // Ünvan
            $table->string('floor_count'); // Mərtəbə sayı
            $table->boolean('is_repaired'); // Təmir statusu: Təmirli/Təmirsiz
            $table->boolean('has_warehouse'); // Anbar sahəsi: Bəli/Xeyr
            $table->boolean('has_parking'); // Avtopark: Bəli/Xeyr
            $table->text('details'); // Ətraflı
            $table->string('file')->nullable(); // Fayl yüklə
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggest_a_place_forms');
    }
};
