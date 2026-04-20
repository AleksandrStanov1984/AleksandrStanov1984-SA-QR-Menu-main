<?php

namespace Database\Seeders;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class SecurityEventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->count() < 2) {
            $this->command->warn('Not enough users to generate security events.');
            return;
        }

        foreach (range(1, 50) as $i) {

            $actor = $users->random();
            $target = $users->random();

            SecurityEvent::factory()
                ->state([
                    'actor_id' => $actor->id,
                    'target_user_id' => $target->id,
                    'restaurant_id' => $target->restaurant_id,
                ])
                ->create();
        }

        $this->command->info('Security events seeded successfully.');
    }
}
