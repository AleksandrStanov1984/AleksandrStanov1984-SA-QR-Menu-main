<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SECTIONS
        Schema::table('sections', function (Blueprint $table) {
            $table->index(['restaurant_id', 'parent_id', 'sort_order'], 'sections_restaurant_parent_sort');
            $table->index(['restaurant_id', 'is_active'], 'sections_active_idx');
        });

        // ITEMS
        Schema::table('items', function (Blueprint $table) {
            $table->index(['section_id', 'is_active', 'sort_order'], 'items_section_active_sort');
        });

        // SECTION TRANSLATIONS
        Schema::table('section_translations', function (Blueprint $table) {
            $table->index(['section_id', 'locale'], 'section_translations_idx');
        });

        // ITEM TRANSLATIONS
        Schema::table('item_translations', function (Blueprint $table) {
            $table->index(['item_id', 'locale'], 'item_translations_idx');
        });

        // RESTAURANTS
        Schema::table('restaurants', function (Blueprint $table) {
            $table->index('slug');
            $table->index('is_active');
            $table->index('plan_key');
        });

        // SOCIAL LINKS
        Schema::table('restaurant_social_links', function (Blueprint $table) {
            $table->index(['restaurant_id', 'is_active'], 'social_active_idx');
            $table->index(['restaurant_id', 'sort_order'], 'social_sort_idx');
        });

        // HOURS
        Schema::table('restaurant_hours', function (Blueprint $table) {
            $table->index(['restaurant_id', 'day_of_week'], 'hours_idx');
        });

        // QR
        Schema::table('restaurant_qr', function (Blueprint $table) {
            $table->index('restaurant_id');
        });

        // TOKENS
        Schema::table('restaurant_tokens', function (Blueprint $table) {
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex('sections_restaurant_parent_sort');
            $table->dropIndex('sections_active_idx');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_section_active_sort');
        });

        Schema::table('section_translations', function (Blueprint $table) {
            $table->dropIndex('section_translations_idx');
        });

        Schema::table('item_translations', function (Blueprint $table) {
            $table->dropIndex('item_translations_idx');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['plan_key']);
        });

        Schema::table('restaurant_social_links', function (Blueprint $table) {
            $table->dropIndex('social_active_idx');
            $table->dropIndex('social_sort_idx');
        });

        Schema::table('restaurant_hours', function (Blueprint $table) {
            $table->dropIndex('hours_idx');
        });

        Schema::table('restaurant_qr', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id']);
        });

        Schema::table('restaurant_tokens', function (Blueprint $table) {
            $table->dropIndex(['token']);
        });
    }
};
