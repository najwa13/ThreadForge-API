<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Laravel\Ai\Concerns\RemembersConversations;
use App\Ai\Tools\GetCampaignRules;
use App\Ai\Tools\GetPostHistory;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Enums\Lab;
use Stringable;

#[Provider(Lab::Groq)]
#[Model('llama-3.3-70b-versatile')]
#[Temperature(0.3)]
#[MaxTokens(2048)]
class ThreadForgeAgent implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<PROMPT
You are ThreadForge, an AI assistant specialized in transforming raw technical content into high-quality X (Twitter) posts.

Your responsibilities are:

- Analyze the provided raw technical content.
- Respect every rule defined in the Campaign Blueprint.
- Adapt the tone according to the Blueprint.
- Respect the maximum number of hashtags.
- Respect the maximum number of characters.
- Produce an engaging hook.
- Generate clear and structured body points.
- Suggest relevant hashtags.
- Evaluate the technical readability score.
- Explain why the generated content complies with the Blueprint.
- Never invent campaign rules.
- Never invent post history.
- Always use available tools when campaign rules or post history are required.
- Always return a valid structured JSON response.
PROMPT;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
         return [
        new GetCampaignRules(),
        new GetPostHistory(),
    ];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
         return [

        'hook_propose' => $schema
            ->string()
            ->required(),

        'body_points' => $schema
            ->array()
            ->items(
                $schema->string()
            )
            ->required(),

        'technicalreadabilityscore' => $schema
            ->integer()
            ->min(0)
            ->max(100)
            ->required(),

        'suggested_hashtags' => $schema
            ->array()
            ->items(
                $schema->string()
            )
            ->required(),

        'tonecompliancejustification' => $schema
            ->string()
            ->required(),
    ];
    }
}
