<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTextBrutRequest;
use App\Http\Requests\UpdateTextBrutRequest;
use App\Http\Resources\TextBrutResource;
use App\Enums\TextBrutStatus;
use App\Models\TextBrut;
use Illuminate\Http\Request;

class TextBrutController extends Controller
{
    /**
     * @group Raw Contents
     *
     * List all raw contents for the authenticated user.
     *
     * @responseField data array The list of raw contents.
     * @responseField data[].id int The raw content ID.
     * @responseField data[].blueprint_id int The linked blueprint ID.
     * @responseField data[].content string The raw text content.
     * @responseField data[].status string The processing status (pending, processed, failed).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TextBrut::class);

        $textBruts = $request->user()
            ->textBruts()
            ->latest()
            ->get();

        return TextBrutResource::collection($textBruts);
    }

    /**
     * @group Raw Contents
     *
     * Submit a new raw content for processing.
     *
     * @bodyParam blueprint_id int required The blueprint ID to apply. Example: 1
     * @bodyParam content string required The raw text content (max 5000 chars). Example: Just shipped a new Laravel feature...
     *
     * @responseField message string The success message.
     * @responseField data object The created raw content.
     *
     * @response 201 {
     *   "message": "Texte brut créé avec succès.",
     *   "data": { "id": 1, "user_id": 1, "blueprint_id": 1, "content": "Just shipped a new Laravel feature...", "status": "pending", "created_at": "2026-06-22T10:00:00.000000Z", "updated_at": "2026-06-22T10:00:00.000000Z" }
     * }
     */
    public function store(StoreTextBrutRequest $request)
    {
          $this->authorize('create', TextBrut::class);

        $textBrut = $request->user()->textBruts()->create([
            ...$request->validated(),
            'status' => TextBrutStatus::PENDING,
        ]);

        return response()->json([
            'message' => 'Texte brut créé avec succès.',
            'data' => new TextBrutResource($textBrut),
        ], 201);
    }

    /**
     * @group Raw Contents
     *
     * Get a single raw content by ID.
     *
     * @urlParameter textBrut int The raw content ID. Example: 1
     *
     * @responseField id int The raw content ID.
     * @responseField blueprint_id int The linked blueprint ID.
     * @responseField content string The raw text content.
     * @responseField status string The processing status.
     */
    public function show(TextBrut $textBrut)
    {
        $this->authorize('view', $textBrut);

        return new TextBrutResource($textBrut);
    }

    /**
     * @group Raw Contents
     *
     * Update an existing raw content.
     *
     * @urlParameter textBrut int The raw content ID. Example: 1
     * @bodyParam blueprint_id int The blueprint ID. Example: 2
     * @bodyParam content string The raw text content. Example: Updated content here...
     * @bodyParam status string The status. Example: pending
     *
     * @responseField message string The success message.
     * @responseField data object The updated raw content.
     */
    public function update(UpdateTextBrutRequest $request, TextBrut $textBrut)
    {
         $this->authorize('update', $textBrut);

        $textBrut->update($request->validated());

        return response()->json([
            'message' => 'Texte brut mis à jour avec succès.',
            'data' => new TextBrutResource($textBrut),
        ]);
    }

    /**
     * @group Raw Contents
     *
     * Delete a raw content.
     *
     * @urlParameter textBrut int The raw content ID. Example: 1
     *
     * @response 200 {
     *   "message": "Texte brut supprimé avec succès."
     * }
     */
    public function destroy(TextBrut $textBrut)
    {
         $this->authorize('delete', $textBrut);

        $textBrut->delete();

        return response()->json([
            'message' => 'Texte brut supprimé avec succès.',
        ]);
    }
}
