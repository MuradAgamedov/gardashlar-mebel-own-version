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
        // Drop foreign key constraints first
        Schema::table('become_a_partner_translates', function (Blueprint $table) {
            $table->dropForeign(['become_a_partner_id']);
        });
        
        // Drop translates table
        Schema::dropIfExists('become_a_partner_translates');
        
        // Drop main table
        Schema::dropIfExists('become_a_partners');
        
        // Create new table with only content
        Schema::create('become_a_partners', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('become_a_partners');
    }
};
