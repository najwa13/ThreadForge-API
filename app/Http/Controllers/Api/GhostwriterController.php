<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\ThreadForgeAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\GhostwriterChatRequest;
use Illuminate\Http\JsonResponse;

class GhostwriterController extends Controller
{
    /**
     * @group Ghostwriter Assistant
     *
     * Chat with the Ghostwriter AI assistant about your generated posts.
     *
     * The assistant can help you refine hooks, generate variants, translate content,
     * or answer questions about your posts. It remembers the conversation context.
     *
     * @bodyParam message string required Your message to the assistant. Example: Give me 3 more aggressive hooks for this post
     * @bodyParam conversation_id string|null The conversation ID to continue. Omit to start a new conversation.
     *
     * @responseField message string The assistant's response.
     * @responseField conversation_id string The conversation ID (use this for follow-up messages).
     *
     * @response 200 {
     *   "message": "Here are 3 more aggressive hooks...",
     *   "conversation_id": "01923456-7890-abcd-ef12-3456789abcde"
     * }
     */
    public function chat(GhostwriterChatRequest $request): JsonResponse
    {
        $agent = new ThreadForgeAgent();

        if ($request->validated('conversation_id')) {
            $agent->continue($request->validated('conversation_id'), $request->user());
        } else {
            $agent->forUser($request->user());
        }

        $response = $agent->prompt($request->validated('message'));

        return response()->json([
            'message' => $response->text(),
            'conversation_id' => $response->conversationId(),
        ]);
    }
}
