<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popup_ad_translates', function (Blueprint $table) {
            $table->string('link')->nullable()->after('image_alt_text');
        });
    }

    public function down(): void
    {
        Schema::table('popup_ad_translates', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
