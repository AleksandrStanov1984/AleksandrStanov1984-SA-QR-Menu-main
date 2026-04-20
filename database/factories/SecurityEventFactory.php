<?php

namespace Database\Factories;

use App\Models\SecurityEvent;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SecurityEvent>
 */
class SecurityEventFactory extends Factory
{
    protected $model = SecurityEvent::class;

    public function definition(): array
    {
        $events = [
            'password_changed',
            'email_changed',
        ];

        return [
            'actor_id' => User::factory(),
            'target_user_id' => User::factory(),

            'restaurant_id' => Restaurant::factory(),

            'event' => $this->faker->randomElement($events),

            'meta' => [
                'mode' => $this->faker->randomElement(['self', 'admin_override']),
                'old_email' => $this->faker->safeEmail(),
                'new_email' => $this->faker->safeEmail(),
            ],

            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Только self изменения
     */
    public function self(): static
    {
        return $this->state(fn () => [
            'meta' => [
                'mode' => 'self',
            ],
        ]);
    }

    /**
     * Только admin override
     */
    public function adminOverride(): static
    {
        return $this->state(fn () => [
            'meta' => [
                'mode' => 'admin_override',
            ],
        ]);
    }

    /**
     * Только password события
     */
    public function password(): static
    {
        return $this->state(fn () => [
            'event' => 'password_changed',
            'meta' => [
                'mode' => $this->faker->randomElement(['self', 'admin_override']),
            ],
        ]);
    }

    /**
     * Только email события
     */
    public function email(): static
    {
        return $this->state(fn () => [
            'event' => 'email_changed',
            'meta' => [
                'mode' => $this->faker->randomElement(['self', 'admin_override']),
                'old_email' => $this->faker->safeEmail(),
                'new_email' => $this->faker->safeEmail(),
            ],
        ]);
    }
}
