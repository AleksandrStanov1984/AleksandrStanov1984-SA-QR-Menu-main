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
        DB::table('users')
            ->whereNotNull('permissions')
            ->orderBy('id')
            ->chunk(100, function ($users) {

                foreach ($users as $user) {
                    $meta = $user->meta ? json_decode($user->meta, true) : [];

                    $existing = $meta['permissions'] ?? [];

                    $old = json_decode($user->permissions, true) ?? [];

                    // merge (старое не затирает новое)
                    $meta['permissions'] = array_merge($existing, $old);

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'meta' => json_encode($meta),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta', function (Blueprint $table) {
            //
        });
    }
};
