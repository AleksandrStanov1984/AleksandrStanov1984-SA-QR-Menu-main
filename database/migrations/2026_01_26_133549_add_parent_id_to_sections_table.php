<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('restaurant_id')
                ->constrained('sections')
                ->nullOnDelete();

            $table->index(['restaurant_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropIndex(['restaurant_id', 'parent_id']);
        });
    }
};
