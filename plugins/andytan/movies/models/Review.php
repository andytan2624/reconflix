<?php namespace Andytan\Movies\Models;

use Model;
use Str;

/**
 * Model
 */
class Review extends Model
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
    public $table = 'andytan_movies_reviews';

    public $belongsTo = [
        'movie' => [
            'Andytan\Movies\Models\Movie',
            'andytan_movies_'
        ]
    ];

    public function beforeSave()
    {
        // Generate a URL slug for this model
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }
}