<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_translates', function (Blueprint $table) {
            $table->longText('weight')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_translates', function (Blueprint $table) {
            $table->string('weight')->nullable()->change(); // əvvəlki halı nə idisə onu yaz
        });
    }
};
