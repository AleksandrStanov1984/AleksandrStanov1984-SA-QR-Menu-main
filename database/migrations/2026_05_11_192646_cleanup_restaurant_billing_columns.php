<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {

            // =========================
            // REMOVE OLD
            // =========================
            $table->dropColumn([
                'active_until',
                'deactivated_at',
                'billing_status',
                'last_payment_confirmed_at',
                'last_payment_period_from',
                'last_payment_period_to',
            ]);

            // =========================
            // ADD NEW
            // =========================
            $table->timestamp('paid_until')
                ->nullable()
                ->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {

            $table->timestamp('active_until')->nullable();
            $table->timestamp('deactivated_at')->nullable();

            $table->string('billing_status', 30)
                ->default('trial');

            $table->timestamp('last_payment_confirmed_at')
                ->nullable();

            $table->timestamp('last_payment_period_from')
                ->nullable();

            $table->timestamp('last_payment_period_to')
                ->nullable();

            $table->dropColumn([
                'paid_until',
            ]);
        });
    }
};
