<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_banners', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot')->nullable()->after('restaurant_id');
        });

        $banners = DB::table('restaurant_banners')
            ->orderBy('restaurant_id')
            ->orderBy('sort_order')
            ->get();

        $currentRestaurant = null;
        $slot = 1;

        foreach ($banners as $banner) {

            if ($currentRestaurant !== $banner->restaurant_id) {
                $currentRestaurant = $banner->restaurant_id;
                $slot = 1;
            }

            DB::table('restaurant_banners')
                ->where('id', $banner->id)
                ->update(['slot' => $slot]);

            $slot++;
        }

        Schema::table('restaurant_banners', function (Blueprint $table) {
            $table->unique(['restaurant_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_banners', function (Blueprint $table) {
            $table->dropUnique(['restaurant_id', 'slot']);
            $table->dropColumn('slot');
        });
    }
};
