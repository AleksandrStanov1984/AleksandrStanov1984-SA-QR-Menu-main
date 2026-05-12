<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('type', 30);
            // trial, payment, manual_activation, manual_deactivation, expiration

            $table->string('status', 30)->default('confirmed');
            // confirmed, pending, cancelled, system

            $table->string('plan_key', 50)->nullable();

            $table->decimal('amount', 8, 2)->nullable();
            $table->string('currency', 3)->default('EUR');

            $table->timestamp('period_from')->nullable();
            $table->timestamp('period_to')->nullable();

            $table->timestamp('confirmed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['restaurant_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['period_from', 'period_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_records');
    }
};
