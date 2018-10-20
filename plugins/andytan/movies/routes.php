<?php

use Andytan\Movies\Models\Actor;
use Andytan\Movies\Models\Genre;
use Andytan\Movies\Models\Movie;

Route::get('seed-actors', function() {
    $faker = Faker\Factory::create();

    for ($i = 0; $i < 5; $i++) {

        Actor::create([
            'name' => $faker->firstName,
            'lastname' => $faker->lastName,
        ]);
    }

    return "Actors created!";
});


Route::get('/populate-movies', function() {
   $faker = Faker\Factory::create();

   $movies = Movie::all();

   foreach ($movies as $movie) {
       $genres = Genre::all()->random(1)->get(0);
       $movie->genres = $genres;

       $movie->created_at = $faker->date($format = 'Y-m-d H:i:s', $max = 'now');
       $movie->published = $faker->boolean($chanceOfGettingTrue = 50);
       $movie->save();
   }
});


Route::get('sitemap.xml', function() {
    $movies = Movie::all();
    $genres = Genre::all();

    return Response::view('andytan.movies::sitemap', ['movies' => $movies, 'genres' => $genres])->header('Content-Type', 'text/xml');
});