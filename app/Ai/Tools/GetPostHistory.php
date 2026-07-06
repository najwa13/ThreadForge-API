<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use App\Models\Post;
use Stringable;

class GetPostHistory implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
         return 'Retrieve the generation history of a post.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
         $post = Post::find($request->integer('post_id'));

    if (! $post) {
        return 'Post not found.';
    }

    return json_encode([
        'id' => $post->id,
        'hook_propose' => $post->hook_propose,
        'body_points' => $post->body_points,
        'technical_readability_score' => $post->technical_readability_score,
        'suggested_hashtags' => $post->suggested_hashtags,
        'tone_compliance_justification' => $post->tone_compliance_justification,
        'status' => $post->status->value,
    ], JSON_PRETTY_PRINT);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
         return [
        'post_id' => $schema
            ->integer()
            ->required(),
    ];
    }
}
