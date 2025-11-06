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
            if (!Schema::hasColumn('vacancy_translates', 'address')) {
                $table->string('address')->nullable()->after('salary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancy_translates', function (Blueprint $table) {
            if (Schema::hasColumn('vacancy_translates', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
