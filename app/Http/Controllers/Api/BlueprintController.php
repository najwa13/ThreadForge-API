<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlueprintRequest;
use App\Http\Requests\UpdateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use App\Models\Blueprint;
use Illuminate\Http\Request;

class BlueprintController extends Controller
{
    /**
     * @group Campaign Blueprints
     *
     * List all campaign blueprints for the authenticated user.
     *
     * @responseField data array The list of blueprints.
     * @responseField data[].id int The blueprint ID.
     * @responseField data[].name string The blueprint name.
     * @responseField data[].tone string The tone rule.
     * @responseField data[].max_hashtags int Max hashtags allowed.
     * @responseField data[].max_characters int Max characters allowed.
     * @responseField data[].regle_supp string|null Extra rules.
     * @responseField data[].posts_count int Number of generated posts.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Blueprint::class);

        $blueprints = $request->user()
            ->blueprints()
            ->withCount('posts')
            ->latest()
            ->get();

        return BlueprintResource::collection($blueprints);
    }

    /**
     * @group Campaign Blueprints
     *
     * Create a new campaign blueprint.
     *
     * @bodyParam name string required The blueprint name. Example: Tech Community
     * @bodyParam tone string required The tone to use. Example: professional yet casual
     * @bodyParam max_hashtags int Maximum number of hashtags. Example: 2
     * @bodyParam max_characters int Maximum characters per post. Example: 280
     * @bodyParam regle_supp string|null Additional rules. Example: No more than 3 emojis
     *
     * @responseField message string The success message.
     * @responseField data object The created blueprint.
     *
     * @response 201 {
     *   "message": "Blueprint créé avec succès.",
     *   "data": { "id": 1, "user_id": 1, "name": "Tech Community", "tone": "professional yet casual", "max_hashtags": 2, "max_characters": 280, "regle_supp": null, "posts_count": 0, "created_at": "2026-06-22T10:00:00.000000Z", "updated_at": "2026-06-22T10:00:00.000000Z" }
     * }
     */
    public function store(StoreBlueprintRequest $request)
    {
        $this->authorize('create', Blueprint::class);

        $blueprint = $request->user()->blueprints()->create($request->validated());
        $blueprint->loadCount('posts');

        return response()->json([
            'message' => 'Blueprint créé avec succès.',
            'data' => new BlueprintResource($blueprint),
        ], 201);
    }

    /**
     * @group Campaign Blueprints
     *
     * Get a single blueprint by ID.
     *
     * @urlParameter blueprint int The blueprint ID. Example: 1
     *
     * @responseField id int The blueprint ID.
     * @responseField name string The blueprint name.
     * @responseField tone string The tone rule.
     * @responseField max_hashtags int Max hashtags allowed.
     * @responseField max_characters int Max characters allowed.
     * @responseField posts_count int Number of generated posts.
     */
    public function show(Blueprint $blueprint)
    {
        $this->authorize('view', $blueprint);

        return new BlueprintResource($blueprint->loadCount('posts'));
    }

    /**
     * @group Campaign Blueprints
     *
     * Update an existing blueprint.
     *
     * @urlParameter blueprint int The blueprint ID. Example: 1
     * @bodyParam name string The blueprint name. Example: Tech Community Updated
     * @bodyParam tone string The tone to use. Example: friendly and technical
     * @bodyParam max_hashtags int Maximum number of hashtags. Example: 3
     * @bodyParam max_characters int Maximum characters per post. Example: 280
     * @bodyParam regle_supp string|null Additional rules. Example: Use emojis sparingly
     *
     * @responseField message string The success message.
     * @responseField data object The updated blueprint.
     */
    public function update(UpdateBlueprintRequest $request, Blueprint $blueprint)
    {
        $this->authorize('update', $blueprint);

        $blueprint->update($request->validated());
        $blueprint->loadCount('posts');

        return response()->json([
            'message' => 'Blueprint mis à jour avec succès.',
            'data' => new BlueprintResource($blueprint),
        ]);
    }

    /**
     * @group Campaign Blueprints
     *
     * Delete a blueprint.
     *
     * @urlParameter blueprint int The blueprint ID. Example: 1
     *
     * @response 200 {
     *   "message": "Blueprint supprimé avec succès."
     * }
     */
    public function destroy(Blueprint $blueprint)
    {
        $this->authorize('delete', $blueprint);

        $blueprint->delete();

        return response()->json([
            'message' => 'Blueprint supprimé avec succès.',
        ]);
    }
}
