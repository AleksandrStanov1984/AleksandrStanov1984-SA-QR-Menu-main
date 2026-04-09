<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // =============================
        // ADMIN
        // =============================
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_super_admin' => true,
                'restaurant_id' => null,
                'meta' => [],
            ]
        );
    }
}
