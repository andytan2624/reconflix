<?php namespace Radiantweb\Problog\Models;

use Model;

/**
 * version Model
 */
class Version extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'radiantweb_problog_versions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = ['post' => ['Radiantweb\Problog\Models\Post']];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
