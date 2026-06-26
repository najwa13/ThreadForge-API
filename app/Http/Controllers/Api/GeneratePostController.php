<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePostJob;
use App\Models\TextBrut;
use Illuminate\Http\JsonResponse;

class GeneratePostController extends Controller
{
    public function generate(TextBrut $textBrut): JsonResponse
    {
        GeneratePostJob::dispatch($textBrut);

        return response()->json([
            'message' => 'La génération du post a été lancée.',
            'status' => 'queued',
            'text_brut_id' => $textBrut->id,
        ], 202);
    }
}