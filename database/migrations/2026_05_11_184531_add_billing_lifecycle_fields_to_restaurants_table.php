<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->timestamp('active_until')->nullable()->after('trial_ends_at');
            $table->timestamp('deactivated_at')->nullable()->after('active_until');

            $table->boolean('keep_data')->default(false)->after('deactivated_at');
            $table->timestamp('keep_data_until')->nullable()->after('keep_data');

            $table->string('billing_status', 30)->default('trial')->after('keep_data_until');

            $table->timestamp('trial_used_at')->nullable()->after('billing_status');

            $table->timestamp('last_payment_confirmed_at')->nullable()->after('trial_used_at');
            $table->timestamp('last_payment_period_from')->nullable()->after('last_payment_confirmed_at');
            $table->timestamp('last_payment_period_to')->nullable()->after('last_payment_period_from');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'active_until',
                'deactivated_at',
                'keep_data',
                'keep_data_until',
                'billing_status',
                'trial_used_at',
                'last_payment_confirmed_at',
                'last_payment_period_from',
                'last_payment_period_to',
            ]);
        });
    }
};
