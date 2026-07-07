<?php

namespace Tests\Feature;

use App\Models\Blueprint;
use App\Models\TextBrut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TextBrutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Blueprint $blueprint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->blueprint = Blueprint::factory()->create(['user_id' => $this->user->id]);
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_list_text_bruts(): void
    {
        TextBrut::factory(3)->create([
            'user_id' => $this->user->id,
            'blueprint_id' => $this->blueprint->id,
        ]);

        $response = $this->getJson('/api/text-bruts');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_text_brut(): void
    {
        $response = $this->postJson('/api/text-bruts', [
            'blueprint_id' => $this->blueprint->id,
            'content' => 'Just shipped a new Laravel feature...',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'blueprint_id', 'content', 'status'],
            ]);

        $this->assertDatabaseHas('text_bruts', [
            'content' => 'Just shipped a new Laravel feature...',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_show_text_brut(): void
    {
        $textBrut = TextBrut::factory()->create([
            'user_id' => $this->user->id,
            'blueprint_id' => $this->blueprint->id,
        ]);

        $response = $this->getJson("/api/text-bruts/{$textBrut->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $textBrut->id]);
    }

    public function test_user_can_update_text_brut(): void
    {
        $textBrut = TextBrut::factory()->create([
            'user_id' => $this->user->id,
            'blueprint_id' => $this->blueprint->id,
        ]);

        $response = $this->putJson("/api/text-bruts/{$textBrut->id}", [
            'content' => 'Updated content here...',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['content' => 'Updated content here...']);
    }

    public function test_user_can_delete_text_brut(): void
    {
        $textBrut = TextBrut::factory()->create([
            'user_id' => $this->user->id,
            'blueprint_id' => $this->blueprint->id,
        ]);

        $response = $this->deleteJson("/api/text-bruts/{$textBrut->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('text_bruts', ['id' => $textBrut->id]);
    }

    public function test_user_cannot_access_other_user_text_brut(): void
    {
        $otherUser = User::factory()->create();
        $textBrut = TextBrut::factory()->create([
            'user_id' => $otherUser->id,
            'blueprint_id' => $this->blueprint->id,
        ]);

        $response = $this->getJson("/api/text-bruts/{$textBrut->id}");

        $response->assertForbidden();
    }

    public function test_create_text_brut_requires_valid_blueprint(): void
    {
        $response = $this->postJson('/api/text-bruts', [
            'blueprint_id' => 9999,
            'content' => 'Some content...',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('blueprint_id');
    }
}
