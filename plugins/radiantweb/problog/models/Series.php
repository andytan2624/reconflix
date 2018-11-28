<?php namespace Radiantweb\Problog\Models;

use Model;

/**
 * Series Model
 */
class Series extends Model
{
	public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
	
    /**
     * @var string The database table used by the model.
     */
    public $table = 'radiantweb_problog_series';
    
    public $translatable = [
        'name'
    ];
    
    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|between:3,64|unique:radiantweb_blog_categories'
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
	    'id'
        ,'name'
        ,'slug'
    ];

    
    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->name);
    }
    
    public function posts()
    {
        return $this->belongsTo('Radiantweb\Problog\Models\Post', 'id', 'series_id')->where('published',1)->orderBy('published_at', 'desc');
    }
}
