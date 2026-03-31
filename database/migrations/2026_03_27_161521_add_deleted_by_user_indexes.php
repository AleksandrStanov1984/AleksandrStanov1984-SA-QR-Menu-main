<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = DB::select("PRAGMA index_list('sections')");
        $exists = collect($indexes)->contains(fn($i) => $i->name === 'sections_deleted_by_user_id_index');

        if (!$exists) {
            Schema::table('sections', function (Blueprint $table) {
                $table->index('deleted_by_user_id');
            });
        }

        $indexes = DB::select("PRAGMA index_list('items')");
        $exists = collect($indexes)->contains(fn($i) => $i->name === 'items_deleted_by_user_id_index');

        if (!$exists) {
            Schema::table('items', function (Blueprint $table) {
                $table->index('deleted_by_user_id');
            });
        }
    }
};
