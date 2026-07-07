<?php

namespace Tests\Feature;

use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BlueprintTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_list_blueprints(): void
    {
        Blueprint::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/blueprints');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_blueprint(): void
    {
        $response = $this->postJson('/api/blueprints', [
            'name' => 'Tech Community',
            'tone' => 'professional',
            'max_hashtags' => 2,
            'max_characters' => 280,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'tone', 'max_hashtags', 'max_characters'],
            ]);

        $this->assertDatabaseHas('blueprints', ['name' => 'Tech Community']);
    }

    public function test_user_can_show_blueprint(): void
    {
        $blueprint = Blueprint::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/blueprints/{$blueprint->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $blueprint->id]);
    }

    public function test_user_can_update_blueprint(): void
    {
        $blueprint = Blueprint::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/blueprints/{$blueprint->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);
    }

    public function test_user_can_delete_blueprint(): void
    {
        $blueprint = Blueprint::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/blueprints/{$blueprint->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('blueprints', ['id' => $blueprint->id]);
    }

    public function test_user_cannot_access_other_user_blueprint(): void
    {
        $otherUser = User::factory()->create();
        $blueprint = Blueprint::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/blueprints/{$blueprint->id}");

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_access_blueprints(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/blueprints');

        $response->assertUnauthorized();
    }
}
