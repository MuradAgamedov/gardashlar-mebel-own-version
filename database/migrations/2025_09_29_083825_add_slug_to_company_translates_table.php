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
        Schema::table('company_translates', function (Blueprint $table) {
            // Əvvəlcə slug sütununu nullable elə ki, migration problemsiz keçsin
            if (!Schema::hasColumn('company_translates', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }
        });

        // Mövcud sətirlər üçün unikal slug dəyərləri doldurmaq
        DB::table('company_translates')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->update([
                'slug' => DB::raw("CONCAT('temp-slug-', id)")
            ]);

        // Slug sütununa unique index əlavə et
        Schema::table('company_translates', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_translates', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
