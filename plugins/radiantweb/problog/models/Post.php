<?php namespace Radiantweb\Problog\Models;

use DB;
use Str;
use Model;
use BackendAuth;
use October\Rain\Database\ModelException;
use Cms\Classes\CmsPropertyHelper;
use System\Classes\PluginManager;
use Cms\Classes\Page;
use Radiantweb\Problog\Models\Tag as BlogTag;
use Radiantweb\Problog\Models\Category as BlogCategory;
use Radiantweb\Problog\Models\Version as BlogVersion;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Markdown;

class Post extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    public $table = 'radiantweb_blog_posts';

    public $purgeable = [
        'post_to_twitter',
        'optimize',
        'post_twitter',
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
        ,'title'
        ,'slug'
        ,'excerpt'
        ,'parent'
        ,'user'
        ,'published_at'
        ,'published'
        ,'categories'
        ,'tags'
    ];

    /*
     * Validation
     */
    public $rules = [
        'title' => 'required',
        'user' => 'required',
        'content' => 'required',
        'categories_id' => 'required'
    ];


    public $customMessages = [
       'categories_id.required' => 'You must select a Category!',
    ];

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User', 'key' => 'user_id'],
        'categories' => ['Radiantweb\Problog\Models\Category', 'key' => 'categories_id'],
        'series' => ['Radiantweb\Problog\Models\Series', 'key' => 'series_id']
    ];

    public $belongsToMany = [
        'tags' => ['Radiantweb\Problog\Models\Tag', 'table' => 'radiantweb_blog_post_tags', 'order' => 'name']
    ];

    public $hasMany = [
        'versions' => ['Radiantweb\Problog\Models\Version','key' => 'current_post_id']
    ];

    public $attachMany = [
        'featured_images' => ['System\Models\File','order' => 'sort_order'],
        'content_images' => ['System\Models\File','order' => 'sort_order']
    ];

    public function getParentOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public static function formatHtml($input)
    {
        $result = Markdown::parse(trim($input));
        return $result;
    }

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->name);
    }

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', '=', 1)
        ;
    }

    /**
     * Returns the logged in user, if available
     */
    public function backEndUser()
    {
        $auth = BackendAuth::getUser();
        return $auth->id;
    }

    public function afterFetch()
    {
        if (ProblogSettingsModel::get('markdownMode', true)) {
            //$this->content_markdown = $this->content;
        }
    }

    public function beforeSave()
    {
        if (ProblogSettingsModel::get('markdownMode', false)) {
            $this->content_markdown = $this->content;
            $this->content = $this->formatHtml($this->content);
        }
    }

    public function afterSave()
    {
        $version_num = DB::table('radiantweb_problog_versions')->where('post_id','=',$this->id)->max('version');
        $version = new BlogVersion;
        $version->post_id = $this->id; // Unset the primary key on the clone instead
        $version->version = ($version_num += 1);
        $version->current_post_id = $this->id; // Unset the primary key on the clone instead
        $version->user_id = $this->user_id;
        $version->series_id = $this->series_id;
        $version->categories_id = $this->categories_id;
        $version->title = $this->title;
        $version->slug = $this->slug;
        $version->parent = $this->parent;
        $version->excerpt = $this->excerpt;
        $version->content = $this->content;
        $version->content_markdown = $this->content_markdown;
        $version->featured_media = $this->featured_media;
        $version->published_at = $this->published_at;
        $version->published = $this->published;
        $version->meta_title = $this->meta_title;
        $version->meta_description = $this->meta_description;
        $version->meta_keywords = $this->meta_keywords;
        $version->save();
        //\Log::info($version);
    }

    public function yearCount($year)
    {
        return 0;
    }

    /**
     * Filter By Popular
     * @return post list
     */
    public function scopeFilterByPopular($query)
    {
        return $query->orderBy('impressions','DESC');
    }

    /**
     * Filter By Trending
     * @return post list
     */
    public function scopeFilterByTrending($query)
    {
        $date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -30 days'));
        $query->whereRaw("published_at > '$date'");
        return $query->orderBy('impressions','desc');
    }

    /**
     * Filter By Series
     * @param type $seriesID
     * @return post list
     */
    public function scopeFilterBySeries($query,$seriesID)
    {
        return $query->where('series_id','=',$seriesID);
    }


     /**
     * Filter By Category
     * @param type $categoryID
     * @return post list
     */
    public function scopeFilterByCategory($query,$categoryID)
    {
        return $query->where('categories_id','=',$categoryID);
    }

    /**
     * Filter By Author
     * @param type $author_id
     * @return post list
     */
    public function scopeFilterByAuthor($query,$author_id)
    {
        return $query->where('user_id','=',$author_id);
    }

    /**
     * Filter By Search
     * @param type $keyword
     * @return post list
     */
    public function scopeFilterBySearch($query,$keyword)
    {
        return $query->whereRaw("MATCH(title,content) AGAINST(? IN BOOLEAN MODE)", array($keyword));
    }

    /**
     * Filter By Date
     * @param type $y
     * @param type $m
     * @param type $d
     * @return post list
     */
    public function scopeFilterByDate($query,$y,$m,$d)
    {
        $query->whereRaw("DATE_FORMAT(published_at,'%Y') = $y");

        if($m){
            $query->whereRaw("DATE_FORMAT(published_at,'%m') = $m");
        }

        if($d){
            $query->whereRaw("DATE_FORMAT(published_at,'%d') = $d");
        }

        return $query;
    }

    /*
     * Kind of long...but it works.
     * duplicate a post
     */
    public static function clonePost($post)
    {
        /*
         * first we make a new product useing all data
         */
        $new_post = new Post;
        $new_post->fill($post->attributes);
        $new_post->id = null; // Unset the primary key on the clone instead
        $new_post->title = $post->title . ' copy';
        $new_post->slug = $post->slug . rand(0, 2000000);
        $new_post->parent = $post->parent;
        $new_post->excerpt = $post->excerpt;
        $new_post->content = $post->content;
        $new_post->content_markdown = $post->content_markdown;
        $new_post->featured_media = $post->featured_media;
        $new_post->published_at = $post->published_at;
        $new_post->published = $post->published;
        $new_post->meta_title = $post->meta_title;
        $new_post->meta_description = $post->meta_description;
        $new_post->meta_keywords = $post->meta_keywords;
        $new_post->user = BackendAuth::getUser();
        $new_post->save();


        /*
         * next we get all price breaks
         * and create new ones using our new
         * product model ID
         */
        $tags = $post->tags;
        foreach($tags as $tag) {
            $data = [
                $new_post->id,
                $tag->id,
            ];

            DB::insert("INSERT INTO radiantweb_blog_post_tags (post_id,tag_id) VALUES (?,?)",$data);
        }

        /*
         * next we get all product images
         * and create new ones using our new
         * product model ID
         */
        $featured_images = $post->featured_images;
        foreach($featured_images as $featured_image) {
            $data = [
                $featured_image->disk_name,
                $featured_image->file_name,
                $featured_image->file_size,
                $featured_image->content_type,
                $featured_image->title,
                $featured_image->description,
                $featured_image->field,
                $new_post->id,
                $featured_image->attachment_type,
                $featured_image->is_public,
                $featured_image->sort_order,
                $featured_image->created_at,
                $featured_image->updated_at,
            ];

            DB::insert("INSERT INTO system_files (
                                    disk_name,
                                    file_name,
                                    file_size,
                                    content_type,
                                    title,
                                    description,
                                    field,
                                    attachment_id,
                                    attachment_type,
                                    is_public,
                                    sort_order,
                                    created_at,
                                    updated_at
                                    ) values (?,?,?,?,?,?,?,?,?,?,?,?,?)", $data);
        }
    }

    /**
     * Check if a plugin exists and is enabled
     * @param plugin ID Namespace.Pluginname
     */
    public static function pluginExists($id)
    {
        $pi = PluginManager::instance();

        if (!$pi->findByIdentifier($id) || $pi->isDisabled($id)){
            return false;
        }

        return true;
    }
}
