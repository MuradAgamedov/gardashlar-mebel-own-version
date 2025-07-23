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
        // Rename emails table to phones
        Schema::rename('emails', 'phones');
        
        // Rename email_types table to phone_types
        Schema::rename('email_types', 'phone_types');
        
        // Update foreign key column name in phones table
        Schema::table('phones', function (Blueprint $table) {
            $table->renameColumn('email_type_id', 'phone_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert foreign key column name
        Schema::table('phones', function (Blueprint $table) {
            $table->renameColumn('phone_type_id', 'email_type_id');
        });
        
        // Rename tables back
        Schema::rename('phone_types', 'email_types');
        Schema::rename('phones', 'emails');
    }
};
