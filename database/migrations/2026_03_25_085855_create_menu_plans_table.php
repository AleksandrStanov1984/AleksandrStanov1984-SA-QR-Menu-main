<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_plans', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique(); // starter, basic, pro
            $table->string('name');          // Starter / Basic / Pro
            $table->decimal('price', 8, 2)->default(0);

            $table->text('description')->nullable();

            // флаги (ВАЖНО для логики UI)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);

            // можно будет потом расширить:
            $table->json('features')->nullable(); // future-proof

            $table->integer('sort_order')->default(100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_plans');
    }
};
