<?php
// database/migrations/2025_10_02_000000_create_roluteete_actions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roluteete_actions', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->integer('percent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roluteete_actions');
    }
};
