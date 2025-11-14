<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popup_ad_translates', function (Blueprint $table) {

            // language_id sütunu əlavə et
            $table->unsignedBigInteger('language_id')
                  ->after('popup_ad_id');

            // Foreign key əlaqəsi
            $table->foreign('language_id')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('popup_ad_translates', function (Blueprint $table) {

            // əvvəl foreign key silinir
            $table->dropForeign(['language_id']);

            // sonra sütun silinir
            $table->dropColumn('language_id');
        });
    }
};
