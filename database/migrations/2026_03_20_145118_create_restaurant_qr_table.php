<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_qr', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('qr_path')->nullable();         // финальный QR
            $table->string('logo_path')->nullable();       // логотип внутри QR
            $table->string('background_path')->nullable(); // фон QR

            $table->json('settings')->nullable();          // цвет, padding и т.д.

            $table->timestamps();

            // 🔥 1 QR на ресторан
            $table->unique('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_qr');
    }
};
