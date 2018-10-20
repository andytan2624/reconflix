<?php namespace Andytan\Movies;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Andytan\Movies\Components\Actors' => 'actors',
            'Andytan\Movies\Components\Actorform' => 'actorform',
            'Andytan\Movies\Components\FilterMovies' => 'filtermovies'
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'AndyTan\Movies\FormWidgets\Actorbox' => [
                'label' => 'Actorbox field',
                'code' => 'actorbox'
            ]
        ];
    }

    public function registerSettings()
    {
    }

    public function boot()
    {
        \Event::listen('offline.sitesearch.query', function ($query) {

            // Search your plugin's contents
            $items = Models\Movie::where('name', 'like', "%${query}%")
                ->orWhere('description', 'like', "%${query}%")
                ->get();

            // Now build a results array
            $results = $items->map(function ($item) use ($query) {

                // If the query is found in the title, set a relevance of 2
                $relevance = mb_stripos($item->title, $query) !== false ? 2 : 1;

                if ($item->poster) {
                    return [
                        'title'     => $item->name,
                        'text'      => $item->description,
                        'url'       => '/movies/movie/' . $item->slug,
                        'thumb'     => $item->poster->first(),
                        'relevance' => $relevance,
                    ];
                } else {
                    return [
                        'title'     => $item->name,
                        'text'      => $item->description,
                        'url'       => '/movies/movie/' . $item->slug,
                        'relevance' => $relevance,
                    ];
                }

            });

            return [
                'provider' => 'Movie', // The badge to display for this result
                'results'  => $results,
            ];
        });
    }
}
