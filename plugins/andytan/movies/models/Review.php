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

  public $rules = [
  ];

  /*
   * Validation
   */
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
  protected $dates = ['deleted_at'];

  public function beforeSave()
  {
    // Generate a URL slug for this model
    if (empty($this->slug)) {
      $this->slug = Str::slug($this->title);
    }
  }


}