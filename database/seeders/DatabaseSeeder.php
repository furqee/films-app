<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Country;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory(5)->create();
        Country::factory(5)->create();
        Genre::factory(5)->create();
        Film::factory(5)->create()->each(function ($film) {
            $genre = Genre::inRandomOrder()->first();
            $user = User::inRandomOrder()->first();
            $film->genres()->attach($genre->id);
            $comment = new Comment;
            $comment->film_id = $film->id;
            $comment->user_id = $user->id;
            $comment->name = 'Test';
            $comment->comment = 'comment';
            $comment->save();
        });
    }
}
