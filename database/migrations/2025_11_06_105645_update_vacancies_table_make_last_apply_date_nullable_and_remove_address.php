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
        Schema::table('vacancies', function (Blueprint $table) {
            // last_apply_date-i nullable etmək
            $table->date('last_apply_date')->nullable()->change();

            // address sütununu silmək
            if (Schema::hasColumn('vacancies', 'address')) {
                $table->dropColumn('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            // Əgər rollback olarsa, last_apply_date yenidən NOT NULL olsun
            $table->date('last_apply_date')->nullable(false)->change();

            // address sütununu geri qaytarmaq
            $table->string('address')->nullable();
        });
    }
};
