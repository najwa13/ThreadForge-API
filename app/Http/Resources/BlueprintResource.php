<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlueprintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'tone' => $this->tone,
            'max_hashtags' => $this->max_hashtags,
            'max_characters' => $this->max_characters,
            'regle_supp' => $this->regle_supp,
            'posts_count' => $this->whenCounted('posts'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
