<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_social_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

            $table->string('title', 120);
            $table->string('url', 2048);

            // svg file path in storage/app/public/...
            $table->string('icon_path')->nullable();

            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index(['restaurant_id', 'sort_order']);
            $table->index(['restaurant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_social_links');
    }
};
