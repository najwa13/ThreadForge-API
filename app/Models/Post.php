<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatus;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
       'text_brut_id','hook_propose','body_points',
       'technical_readability_score','suggested_hashtags',
       'tone_compliance_justification','payload_brut','status',
    ];

    protected $casts = [
        'body_points' => 'array',
        'suggested_hashtags' => 'array',
        'technical_readability_score' => 'integer',
        'status' => PostStatus::class,
    ];

    
    
    public function textBrut(){
        return $this->belongsTo(TextBrut::class);
    }

}
