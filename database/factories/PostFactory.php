<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\TextBrut;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text_brut_id' => TextBrut::factory(),
            'hook_propose' => fake()->sentence(),
            'body_points' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'technical_readability_score' => fake()->numberBetween(0, 100),
            'suggested_hashtags' => [fake()->word(), fake()->word()],
            'tone_compliance_justification' => fake()->paragraph(),
            'payload_brut' => fake()->paragraphs(2, true),
            'status' => 'draft',
        ];
    }
}
