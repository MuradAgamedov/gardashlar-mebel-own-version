<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // discount_percent sütununu silirik
            $table->dropColumn('discount_percent');

            // yeni discount_price sütunu əlavə edirik
            $table->decimal('discount_price', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Əgər rollback edilsə, əvvəlki vəziyyətə qaytar
            $table->integer('discount_percent')->default(0);
            $table->dropColumn('discount_price');
        });
    }
};
