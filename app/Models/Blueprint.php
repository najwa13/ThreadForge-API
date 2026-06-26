<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\TextBrut;


class Blueprint extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','tone','max_hashtags','max_characters','regle_supp',];

    protected $casts = [
        'max_hashtags' => 'integer',
        'max_characters' => 'integer',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function textBruts(){
        return $this->hasMany(TextBrut::class);
    }

     public function posts()
    {
        return $this->hasManyThrough(Post::class, TextBrut::class);
    }
}
