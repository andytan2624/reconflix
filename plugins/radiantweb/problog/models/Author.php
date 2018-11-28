<?php namespace Radiantweb\ProBlog\Models;

use Model;

/**
 * Author Model
 */
class Author extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'radiantweb_problog_authors';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User'],
    ];
    
    public static function getFromUser($user)
    {
        if($user->author)
            return $user->author;

        $author = new static;
        $author->user = $user;

        if ($user->exists) {
            $user->author()->save($author);
        }

        return $author;
    }
}
