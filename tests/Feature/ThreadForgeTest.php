<?php

use App\Jobs\GeneratePostJob;
use App\Models\Blueprint;
use App\Models\TextBrut;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

it('returns a token with valid login', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'secret123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email'], 'token']);
});

it('returns 401 with wrong password', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Identifiants invalides.']);
});

it('blocks unauthenticated access to blueprints and allows with token', function () {
    $response = $this->getJson('/api/blueprints');
    $response->assertUnauthorized();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Blueprint::factory(2)->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/blueprints');
    $response->assertOk()
        ->assertJsonStructure(['data']);
});

it('returns 422 when creating a blueprint without required name', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/blueprints', [
        'tone' => 'professional',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('name');
});

it('dispatches GeneratePostJob and returns 202 on generation endpoint', function () {
    Queue::fake();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $blueprint = Blueprint::factory()->create(['user_id' => $user->id]);
    $textBrut = TextBrut::factory()->create([
        'user_id' => $user->id,
        'blueprint_id' => $blueprint->id,
    ]);

    $response = $this->postJson("/api/text-bruts/{$textBrut->id}/generate");

    $response->assertStatus(202)
        ->assertJsonStructure(['message', 'status', 'text_brut_id'])
        ->assertJson(['status' => 'queued', 'text_brut_id' => $textBrut->id]);

    Queue::assertPushed(GeneratePostJob::class, function ($job) use ($textBrut) {
        return $job->textBrut->id === $textBrut->id;
    });
});
