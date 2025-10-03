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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Əlaqələndirilmiş istifadəçi (nullable — guest order dəstəklənir)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->string('full_name');
            $table->string('phone');
            $table->string('email');
            $table->string('city');
            $table->string('order_id')->unique();
            $table->text('text')->nullable(); // max söz limiti validation-da qoyulacaq
            $table->string('delivery_method'); // çatdırılma üsulu
            $table->string('payment_method');  // ödəniş üsulu
            $table->string('selected_card');   // seçilmiş kart
            $table->integer('month')->nullable(); // ay (nullable)
            $table->decimal('monthly_price', 10, 2)->nullable(); // aylıq ödəniş

            $table->string('status')->default('pending');         // sifariş statusu
            $table->string('payment_status')->default('unpaid');  // ödəniş statusu

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
