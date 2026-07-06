<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @group Generated Posts
     *
     * List all generated posts for the authenticated user.
     *
     * @responseField data array The list of posts.
     * @responseField data[].id int The post ID.
     * @responseField data[].text_brut_id int The linked raw content ID.
     * @responseField data[].hook_propose string The generated hook.
     * @responseField data[].body_points array The body points.
     * @responseField data[].technical_readability_score int Readability score (0-100).
     * @responseField data[].suggested_hashtags array Suggested hashtags.
     * @responseField data[].tone_compliance_justification string Why it complies with the blueprint.
     * @responseField data[].status string The post status (draft, archived, posted).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::whereHas('textBrut', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
            ->with('textBrut.blueprint')
            ->latest()
            ->get();

        return PostResource::collection($posts);
    }

    /**
     * @group Generated Posts
     *
     * Get a single generated post by ID.
     *
     * @urlParameter post int The post ID. Example: 1
     *
     * @responseField id int The post ID.
     * @responseField hook_propose string The generated hook.
     * @responseField body_points array The body points.
     * @responseField technical_readability_score int Readability score (0-100).
     * @responseField suggested_hashtags array Suggested hashtags.
     * @responseField tone_compliance_justification string Why it complies with the blueprint.
     * @responseField status string The post status.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        $post->load('textBrut.blueprint');

        return new PostResource($post);
    }

    /**
     * @group Generated Posts
     *
     * Update a post (typically to change its status).
     *
     * @urlParameter post int The post ID. Example: 1
     * @bodyParam status string required The new status. Must be one of: draft, archived, posted. Example: posted
     *
     * @responseField message string The success message.
     * @responseField data object The updated post.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json([
            'message' => 'Post mis à jour avec succès.',
            'data' => new PostResource($post),
        ]);
    }

    /**
     * @group Generated Posts
     *
     * Delete a generated post.
     *
     * @urlParameter post int The post ID. Example: 1
     *
     * @response 200 {
     *   "message": "Post supprimé avec succès."
     * }
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post supprimé avec succès.',
        ]);
    }
}
