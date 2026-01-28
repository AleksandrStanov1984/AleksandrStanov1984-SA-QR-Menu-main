<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('title_font')->nullable()->after('key');
            $table->string('title_color', 7)->nullable()->after('title_font'); // #RRGGBB
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['title_font', 'title_color']);
        });
    }
};
