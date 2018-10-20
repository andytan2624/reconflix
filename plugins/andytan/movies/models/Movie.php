<?php namespace Andytan\Movies\Models;

use Model;

/**
 * Model
 */
class Movie extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andytan_movies_';

    /* Relations */

    public $belongsToMany = [
        'genres' => [
            'Andytan\Movies\Models\Genre',
            'table' => 'andytan_movies_movies_genres',
            'order' => 'genre_title'
        ],
        'actors' => [
            'Andytan\Movies\Models\Actor',
            'table' => 'andytan_movies_actors_movies',
            'order' => 'name'
        ]
    ];

    public $hasMany = [
        'reviews' => [
            'Andytan\Movies\Models\Review',
            'table' => 'andytan_movies_reviews'
        ]
    ];

    public $attachOne = [
        'poster' => 'System\Models\File'
    ];

    public $attachMany = [
        'movie_gallery' => 'System\Models\File'
    ];

    public static $allowedSortingOptions = array (
        'name desc' => 'Name - desc',
        'name asc' => 'Name - asc',
        'year desc' => 'Year - desc',
        'year asc' => 'Year - asc',
    );


    public function scopeListFrontEnd($query, $options = []) {
        extract(array_merge([
            'page' => 1,
            'perPage' => 10,
            'sort' => 'created_at desc',
            'genres' => null,
            'year' => ''
        ], $options));

        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {
            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);

                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }

                list($sortField, $sortDirection) = $parts;

                $query->orderBy($sortField, $sortDirection);
            }
        }

        if ($genres !== null) {
            if (!is_array($genres)) {
                $genres = [$genres];
            }

            foreach ($genres as $genre) {
                $query->whereHas('genres', function($q) use ($genre) {
                   $q->where('id', '=', $genre);
                });
            }
        }

        $lastPage = $query->paginate($perPage, $page)->lastPage();

        if ($lastPage < $page) {
            $page = 1;
        }

        if ($year) {
            $query->where('year', '=', $year);
        }

        return $query->paginate($perPage, $page);

    }

}