<?php

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\TextBrut;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TextBrut>
 */
class TextBrutFactory extends Factory
{
    protected $model = TextBrut::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'blueprint_id' => Blueprint::factory(),
            'content' => fake()->paragraphs(3, true),
            'status' => 'pending',
        ];
    }
}
