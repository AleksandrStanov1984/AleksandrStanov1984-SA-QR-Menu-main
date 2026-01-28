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
        Schema::create('restaurants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();

                $table->string('template_key')->default('classic'); // classic|fastfood|bar|services
                $table->string('default_locale')->default('de');
                $table->json('enabled_locales')->nullable(); // ["de","en","ru"]

                $table->json('theme_tokens')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('background_path')->nullable();
                $table->text('custom_css')->nullable();

                $table->boolean('is_active')->default(true);

                $table->date('trial_ends_at')->nullable();
                $table->string('plan_key')->nullable(); // small|restaurant|custom
                $table->decimal('monthly_price', 8, 2)->nullable();

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
