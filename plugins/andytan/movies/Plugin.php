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
            $reviews = Models\Review::where('title', 'like', "%${query}%")
                ->orWhere('excerpt', 'like', "%${query}%")
                ->orWhere('content_html', 'like', "%${query}%")
                ->get();

            // Now build a results array
            $results = $reviews->map(function ($review) use ($query) {

                // If the query is found in the title, set a relevance of 2
                $relevance = mb_stripos($review->title, $query) !== false ? 2 : 1;

                if ($review->movie->poster) {
                    return [
                        'title'     => $review->title,
                        'text'      => $review->excerpt,
                        'url'       => '/review/' . $review->slug,
                        'thumb'     => $review->movie->poster->first(),
                        'relevance' => $relevance,
                    ];
                } else {
                    return [
                        'title'     => $review->title,
                        'text'      => $review->excerpt,
                        'url'       => '/review/' . $review->slug,
                        'relevance' => $relevance,
                    ];
                }

            });

            return [
//                'provider' => '', // The badge to display for this result
                'results'  => $results,
            ];
        });
    }
}
