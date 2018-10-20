<?php
namespace Andytan\Movies\Components;

use Andytan\Movies\Models\Genre;
use Cms\Classes\ComponentBase;
use Input;
use Andytan\Movies\Models\Movie;

class FilterMovies extends ComponentBase
{

    public $movies;
    public $genres;
    public $years;

    public function componentDetails()
    {
        return [
            'name'=> ' Actor Form',
            'description' => 'Enter Actors'
        ];
    }
    
    public function onRun() {
        $this->movies = $this->filterMovies();
        $this->genres = Genre::all();
        $this->years = $this->filterYears();
    }

    public function filterYears() {
        $query = Movie::all();

        $years = [];

        foreach ($query as $movie) {
            $years[] = $movie->year;
        }

        $years = array_unique($years);
        asort($years);

        return $years;
    }

    protected function filterMovies() {
        $year = Input::get('year');
        $genre = Input::get('genre');

        $query = Movie::all();

        if ($year) {
            $query = Movie::where('year', '=', $year)->get();
        }

        if ($genre) {
            $query = Movie::whereHas('genres', function($filter) use ($genre) {
                $filter->where('slug', '=', $genre);
            })->get();
        }

        if ($year && $genre) {
            $query = Movie::whereHas('genres', function($filter) use ($genre) {
                $filter->where('slug', '=', $genre);
            })->where('year', '=', $year)->get();
        }

        return $query;
    }
}