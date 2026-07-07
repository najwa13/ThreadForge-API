<?php

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Blueprint>
 */
class BlueprintFactory extends Factory
{
    protected $model = Blueprint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'tone' => fake()->randomElement(['professional', 'casual', 'friendly', 'technical', 'humorous']),
            'max_hashtags' => fake()->numberBetween(1, 5),
            'max_characters' => fake()->randomElement([280, 300, 500]),
            'regle_supp' => fake()->optional()->sentence(),
        ];
    }
}
