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
            // Əgər istəsəniz sahəni müəyyən bir sütundan sonra əlavə edə bilərsiniz:
            // $table->decimal('amount', 10, 2)->default(0)->after('some_column');
            
            // Sadə əlavə:
            $table->decimal('amount', 10, 2)->default(0)->comment('Order total amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
