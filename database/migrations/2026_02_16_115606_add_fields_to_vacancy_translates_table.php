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
        Schema::table('vacancy_translates', function (Blueprint $table) {
            $table->string('education')->nullable();
            $table->string('related_person')->nullable();
            $table->string('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancy_translates', function (Blueprint $table) {
            $table->dropColumn(['education', 'related_person', 'city']);
        });
    }
};
