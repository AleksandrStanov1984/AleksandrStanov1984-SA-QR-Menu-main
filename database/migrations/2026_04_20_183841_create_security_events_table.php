<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();

            // кто сделал действие
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();

            // над кем действие
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();

            // tenant (опционально)
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();

            // тип события
            $table->string('event');

            // доп данные
            $table->json('meta')->nullable();

            // ip / user agent
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // индексы
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
