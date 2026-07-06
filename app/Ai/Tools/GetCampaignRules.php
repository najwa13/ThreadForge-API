<?php

namespace App\Ai\Tools;

use App\Models\Blueprint;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCampaignRules implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Retrieve the rules and configuration of a campaign blueprint.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $blueprint = Blueprint::find($request->integer('blueprint_id'));

        if (! $blueprint) {
            return 'Blueprint not found.';
        }

        return json_encode([
            'id' => $blueprint->id,
            'name' => $blueprint->name,
            'tone' => $blueprint->tone,
            'max_hashtags' => $blueprint->max_hashtags,
            'max_characters' => $blueprint->max_characters,
            'regle_supp' => $blueprint->regle_supp,
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'blueprint_id' => $schema
                ->integer()
                ->required(),
        ];
    }
}