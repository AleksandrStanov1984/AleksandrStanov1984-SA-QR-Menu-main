<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SectionTranslation;
use App\Models\Section;

class SectionTranslationFactory extends Factory
{
    protected $model = SectionTranslation::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'locale' => 'de',
            'title' => fake()->word(),
        ];
    }
}
