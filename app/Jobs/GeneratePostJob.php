<?php

namespace App\Jobs;

use App\Models\TextBrut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePostJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TextBrut $textBrut
    ) {
    }

    public function handle(): void
    {
        Log::info('GeneratePostJob started', [
            'text_brut_id' => $this->textBrut->id,
            'content' => $this->textBrut->content,
        ]);

        // Ici sera ajouté Laravel AI dans le Sprint 2.
    }
}