<?php

namespace App\Jobs;

use App\Models\TextBrut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Ai\Agents\ThreadForgeAgent;
use App\Enums\PostStatus;
use App\Enums\TextBrutStatus;
use App\Models\Post;
use Throwable;

class GeneratePostJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TextBrut $textBrut
    ) {
    }

    public function handle(): void
    {
        try {
            Log::info('GeneratePostJob started', [
                'text_brut_id' => $this->textBrut->id,
            ]);

            $response = (new ThreadForgeAgent())
                ->prompt($this->textBrut->content);

            $data = $response->data();

            Post::create([
                'text_brut_id' => $this->textBrut->id,
                'hook_propose' => $data['hook_propose'],
                'body_points' => $data['body_points'],
                'technical_readability_score' => $data['technicalreadabilityscore'],
                'suggested_hashtags' => $data['suggested_hashtags'],
                'tone_compliance_justification' => $data['tonecompliancejustification'],
                'payload_brut' => (string) $response,
                'status' => PostStatus::DRAFT,
            ]);

            $this->textBrut->update([
                'status' => TextBrutStatus::PROCESSED,
            ]);

            Log::info('GeneratePostJob completed', [
                'text_brut_id' => $this->textBrut->id,
            ]);

        } catch (Throwable $e) {
            Log::error('GeneratePostJob failed', [
                'text_brut_id' => $this->textBrut->id,
                'error' => $e->getMessage(),
            ]);

            $this->textBrut->update([
                'status' => TextBrutStatus::FAILED,
            ]);
        }
    }
}
