<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\ThreadForgeAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\GhostwriterChatRequest;
use Illuminate\Http\JsonResponse;

class GhostwriterController extends Controller
{
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
