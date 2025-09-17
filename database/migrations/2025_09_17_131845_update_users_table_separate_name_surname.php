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
        Schema::table('users', function (Blueprint $table) {
            // Add separate name and surname fields
            $table->string('name')->after('id');
            $table->string('surname')->after('name');
            
            // Drop the full_name column
            $table->dropColumn('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back full_name column
            $table->string('full_name')->after('id');
            
            // Drop separate name and surname columns
            $table->dropColumn(['name', 'surname']);
        });
    }
};
