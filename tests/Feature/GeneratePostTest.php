<?php

namespace Tests\Feature;

use App\Models\TextBrut;
use App\Models\Blueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GeneratePostJob;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GeneratePostTest extends TestCase
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

    public function test_generate_dispatches_job_and_returns_202(): void
    {
        Queue::fake();

        $response = $this->postJson("/api/text-bruts/{$this->textBrut->id}/generate");

        $response->assertStatus(202)
            ->assertJsonStructure([
                'message',
                'status',
                'text_brut_id',
            ])
            ->assertJson([
                'status' => 'queued',
                'text_brut_id' => $this->textBrut->id,
            ]);

        Queue::assertPushed(GeneratePostJob::class, function ($job) {
            return $job->textBrut->id === $this->textBrut->id;
        });
    }
}
