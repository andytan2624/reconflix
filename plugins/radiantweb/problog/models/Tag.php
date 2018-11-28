<?php namespace Radiantweb\Problog\Models;

use Str;
use Model;
use Radiantweb\Problog\Models\Post;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use October\Rain\Router\Helper as RouterHelper;
use URL;

class Tag extends Model
{
    public $table = 'radiantweb_blog_tags';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'name'
    ];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|between:3,64|unique:radiantweb_blog_tags',
        'code' => 'unique:radiantweb_blog_tags',
    ];

    protected $guarded = [];

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->name);
    }

    /**
     * @return query
     */
    public function getRelatedTagsBaseQuery()
    {
        return Post::join('radiantweb_blog_post_tags', 'radiantweb_blog_post_tags.post_id', '=', 'radiantweb_blog_posts.id')
                    ->select('radiantweb_blog_posts.*', 'radiantweb_blog_post_tags.tag_id', 'radiantweb_blog_post_tags.post_id');
    }

    public function scopePosts()
    {
        // @todo: declare this relationship as the class field when the conditions option is implemented
        return $this->belongsToMany('Radiantweb\Problog\Models\Post', 'radiantweb_blog_post_tags')->where('published',1)->orderBy('published_at', 'desc');
    }

    public function postCount()
    {
        return $this->posts()->count();
    }

    public static function resolveMenuItem($item, $url, $theme)
    {
        if ($item->type == 'problog-tag') {
            $Item = [
                'url' => static::getTagRenderUrl($theme, $item),
                'isActive' => 1,
                'items' => static::getBlogTagRenderUrls($theme, $item, true)
            ];
        }
        else {
            $Item = [
                'url' => static::getTagRenderUrl($theme, $item),
                'isActive' => 1,
                'items' => static::getBlogTagRenderUrls($theme, $item, true)
            ];
        }

        return $Item;
    }

    public static function getMenuTypeInfo($type)
    {
        $tags = Tag::lists('slug', 'name');

        if ($type == 'problog-tag') {
            $item = Array (
                'dynamicItems'  => 0,
                'nesting'       => 0,
                'references'    => $tags,
                'cmsPages'      => static::getTagRenderPages()
            );
        }
        else {
            $item = Array (
                'dynamicItems'  => true,
                'cmsPages'      => static::getTagRenderPages()
            );
        }

        return $item;
    }

    private static function getTagRenderUrl($theme, $item)
    {
        $tag = Tag::where('name',$item->reference)->first();
        $page = CmsPage::loadCached($theme, $item->cmsPage);

        // Always check if the page can be resolved
        if (!$page)
            return;

        $url = null;

        if (!$tag) {
            $options = ['filter'=>null,'slug' => null];
        }
        else {
            $options = ['filter'=>'tag','slug' => $tag->slug];
        }

        // Generate the URL
        $url = CmsPage::url($page->getBaseFileName(), $options , false);

        $url = URL::to(Str::lower(RouterHelper::normalizeUrl($url))).'/';

        return $url;
    }

    private static function getBlogTagRenderUrls($theme, $item, $alltags=false)
    {
        $page = CmsPage::loadCached($theme, $item->cmsPage);
        $result = [];
        $tags = Tag::lists('slug', 'name');

        $pages = [];

        if($item->nesting > 0){
            foreach($tags as $slug=>$name) {

                if($alltags){
                    $url = CmsPage::url($page->getBaseFileName(), ['filter'=>'tag','slug' => $slug], false);
                    $url = URL::to(Str::lower($url)).'/';
                    $pages[] = array(
                        'title' => $name,
                        'url' => $url,
                    );
                }else{
                    $category = Tag::whereRaw("LOWER(slug) = '$slug'")->first();
                    $tagPages = Post::filterByCategory($category->id)->get()->all();

                    $pageUrl = CmsPage::url($page->getBaseFileName(), ['slug' => $slug], false);
                    $pageUrl = str_replace('/default','', Str::lower($pageUrl).'/');

                    foreach($tagPages as $cpage){
                        $pages[] = array(
                            'title'=>$cpage->title,
                            'url'=> Str::lower($pageUrl).$cpage->slug.'/',
                        );
                    }
                }
            }
        }

        return $pages;
    }

    private static function getTagRenderPages()
    {
        $result = [];

        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);

        $cmsPages = [];
        foreach ($pages as $page) {
            if (!$page->hasComponent('proBlogList'))
                continue;

            $cmsPages[] = $page;
        }

        return $cmsPages;
    }
}
