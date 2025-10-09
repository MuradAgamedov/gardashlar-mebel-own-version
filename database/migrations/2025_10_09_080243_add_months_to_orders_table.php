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
        Schema::table('orders', function (Blueprint $table) {
            // 🔹 months sahəsi əlavə olunur
            if (!Schema::hasColumn('orders', 'months')) {
                $table->integer('months')->nullable()->after('monthly_price')->comment('Installment months count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'months')) {
                $table->dropColumn('months');
            }
        });
    }
};
