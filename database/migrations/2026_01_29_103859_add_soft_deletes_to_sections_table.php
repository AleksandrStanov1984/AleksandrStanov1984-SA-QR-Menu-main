<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('sections', 'deleted_by_user_id')) {
                $table->unsignedBigInteger('deleted_by_user_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('sections', 'deleted_by_user_id')) {
                $table->dropColumn('deleted_by_user_id');
            }
        });
    }
};
