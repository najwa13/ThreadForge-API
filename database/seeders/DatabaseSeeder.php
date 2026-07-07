<?php

namespace Database\Seeders;

use App\Models\Blueprint;
use App\Models\Post;
use App\Models\TextBrut;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $blueprints = Blueprint::factory(2)->create([
            'user_id' => $user->id,
        ]);

        $textBruts = TextBrut::factory(3)->create([
            'user_id' => $user->id,
            'blueprint_id' => $blueprints->first()->id,
            'status' => 'processed',
        ]);

        Post::factory(2)->create([
            'text_brut_id' => $textBruts->first()->id,
            'status' => 'draft',
        ]);
    }
}
