<?php namespace Radiantweb\Problog\Classes;

use Radiantweb\Problog\Models\Post;
use Radiantweb\Problog\Models\Category;
use Radiantweb\Problog\Models\Tag;

/**
 * print out rss views
 * Requires min php 5.3  
 *
 * @package radiantweb/problog
 * @author ChadStrat
 */
class ProblogRssFeed
{
    public function __construct($type=null)
    {
        return Post::get()->all();
    }
    
    public static function getFeed(){
        return Post::get()->all();
    }

    public static function getCategoryFeed($slug=null){
        $category = Category::whereRaw("LOWER(name) = '$slug'")->first();
        if($category){
            return Post::isPublished()->where('categories_id','=',$category->id)->orderBy('published_at', 'desc');
        }
    }

    public static function getTagFeed($slug=null){
        $tag = Tag::where('slug', '=', $slug)->first();
        if($tag){
            return Post::isPublished()->where('categories_id','=',$category->id)->orderBy('published_at', 'desc');
        }
    }

}