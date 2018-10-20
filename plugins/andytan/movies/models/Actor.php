<?php namespace Andytan\Movies\Models;

use Model;

/**
 * Model
 */
class Actor extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'lastname'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'andytan_movies_actors';

    /* Relations */

    public $belongsToMany = [

        'actors' => [
            'Andytan\Movies\Models\Movie',
            'table' => 'andytan_movies_actors_movies',
            'order' => 'name'
        ]
    ];

    public $attachOne = [
        'actorimage' => 'System\Models\File'
    ];

    public function getFullNameAttribute() {
        return $this->name . " " . $this->lastname;
    }
}