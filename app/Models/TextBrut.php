<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TextBrutStatus;
use App\Models\Post;
use App\Models\Blueprint;
use App\Models\User;

class TextBrut extends Model
{
    use HasFactory;

    protected $table = 'text_bruts';

    protected $fillable = ['user_id','blueprint_id','content','status'];

    protected function casts(): array
{
    return [ 'status' => TextBrutStatus::class, ];
}


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function blueprint(){
        return $this->belongsTo(Blueprint::class);
    }

    public function post(){
        return $this->hasOne(Post::class);
    }
}
