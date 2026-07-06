<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePostJob;
use App\Models\TextBrut;
use Illuminate\Http\JsonResponse;

class GeneratePostController extends Controller
{
    /**
     * @group Post Generation
     *
     * Submit a raw content for asynchronous AI post generation.
     *
     * The generation is processed in the background via a queue job.
     * Returns immediately with a 202 Accepted status.
     *
     * @urlParameter textBrut int The raw content ID to process. Example: 1
     *
     * @responseField message string The status message.
     * @responseField status string Always "queued".
     * @responseField text_brut_id int The raw content ID being processed.
     *
     * @response 202 {
     *   "message": "La génération du post a été lancée.",
     *   "status": "queued",
     *   "text_brut_id": 1
     * }
     */
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
