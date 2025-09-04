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
        Schema::table('service_translates', function (Blueprint $table) {
            $table->string('advantages_title')->nullable()->after('form_top_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_translates', function (Blueprint $table) {
            $table->dropColumn('advantages_title');
        });
    }
};
