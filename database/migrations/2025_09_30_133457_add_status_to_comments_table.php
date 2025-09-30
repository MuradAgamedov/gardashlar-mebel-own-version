<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Əgər is_read sütunu mövcud deyilsə əlavə et
            if (!Schema::hasColumn('comments', 'is_read')) {
                $table->enum('is_read', ['active', 'passive'])->default('passive')->after('text');
            }
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
