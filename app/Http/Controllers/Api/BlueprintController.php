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

    public function show(Blueprint $blueprint)
    {
        $this->authorize('view', $blueprint);

        return new BlueprintResource($blueprint->loadCount('posts'));
    }

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

    public function destroy(Blueprint $blueprint)
    {
        $this->authorize('delete', $blueprint);

        $blueprint->delete();

        return response()->json([
            'message' => 'Blueprint supprimé avec succès.',
        ]);
    }
}