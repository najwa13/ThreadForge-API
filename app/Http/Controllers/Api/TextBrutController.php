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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TextBrut::class);

        $textBruts = $request->user()
            ->textBruts()
            ->with('blueprint')
            ->latest()
            ->get();

        return TextBrutResource::collection($textBruts);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(TextBrut $textBrut)
    {
        $this->authorize('view', $textBrut);

        return new TextBrutResource($textBrut);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
