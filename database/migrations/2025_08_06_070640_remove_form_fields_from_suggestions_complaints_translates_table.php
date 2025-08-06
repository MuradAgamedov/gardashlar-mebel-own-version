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
        Schema::table('suggestions_complaints_translates', function (Blueprint $table) {
            $table->dropColumn(['form_title', 'form_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggestions_complaints_translates', function (Blueprint $table) {
            $table->string('form_title');
            $table->text('form_description');
        });
    }
};
