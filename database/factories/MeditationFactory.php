<?php

namespace Database\Factories;

use App\Models\Meditation;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeditationFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Meditation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(random_int(1, 3), true),
            'duration' => $this->faker->numberBetween(120, 5400),
        ];
    }
}
