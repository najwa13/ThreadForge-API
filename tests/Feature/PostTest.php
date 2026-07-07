<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\TextBrut;
use App\Models\Blueprint;
use App\Models\User;
use App\Enums\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected TextBrut $textBrut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $blueprint = Blueprint::factory()->create(['user_id' => $this->user->id]);
        $this->textBrut = TextBrut::factory()->create([
            'user_id' => $this->user->id,
            'blueprint_id' => $blueprint->id,
        ]);
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_list_posts(): void
    {
        Post::factory(3)->create(['text_brut_id' => $this->textBrut->id]);

        $response = $this->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_show_post(): void
    {
        $post = Post::factory()->create(['text_brut_id' => $this->textBrut->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $post->id]);
    }

    public function test_user_can_update_post_status(): void
    {
        $post = Post::factory()->create([
            'text_brut_id' => $this->textBrut->id,
            'status' => 'draft',
        ]);

        $response = $this->patchJson("/api/posts/{$post->id}", [
            'status' => 'posted',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'posted']);
    }

    public function test_user_can_delete_post(): void
    {
        $post = Post::factory()->create(['text_brut_id' => $this->textBrut->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_access_other_user_post(): void
    {
        $otherUser = User::factory()->create();
        $otherBlueprint = Blueprint::factory()->create(['user_id' => $otherUser->id]);
        $otherTextBrut = TextBrut::factory()->create([
            'user_id' => $otherUser->id,
            'blueprint_id' => $otherBlueprint->id,
        ]);
        $post = Post::factory()->create(['text_brut_id' => $otherTextBrut->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertForbidden();
    }

    public function test_update_post_requires_valid_status(): void
    {
        $post = Post::factory()->create(['text_brut_id' => $this->textBrut->id]);

        $response = $this->patchJson("/api/posts/{$post->id}", [
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }
}
