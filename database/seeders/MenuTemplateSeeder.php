<?php

namespace Database\Seeders;

use App\Models\MenuTemplate;
use Illuminate\Database\Seeder;

class MenuTemplateSeeder extends Seeder
{
    public function run(): void
    {
        MenuTemplate::updateOrCreate(
            ['key' => 'united'],
            [
                'name' => 'United',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
