<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GhostwriterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_unauthenticated_user_cannot_chat(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->postJson('/api/ghostwriter/chat', [
            'message' => 'Hello',
        ]);

        $response->assertUnauthorized();
    }

    public function test_chat_requires_message(): void
    {
        $response = $this->postJson('/api/ghostwriter/chat', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('message');
    }

    public function test_chat_accepts_conversation_id(): void
    {
        $response = $this->postJson('/api/ghostwriter/chat', [
            'message' => 'Hello',
            'conversation_id' => '01923456-7890-abcd-ef12-3456789abcde',
        ]);

        // Will fail due to AI provider not being available in tests,
        // but validates the request is accepted
        $response->assertSuccessful();
    }
}
