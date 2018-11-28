<?php namespace Radiantweb\Problog\Models;

use Str;
use URL;
use File;
use Model;
use Input;
use October\Rain\Router\Helper as RouterHelper;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Cms\Classes\Content;
use Cms\Classes\Page;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Cms\Classes\Theme as PageTheme;
use ApplicationException;
use Radiantweb\Problog\Models\Post;

class Category extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'radiantweb_blog_categories';

    public $translatable = [
        'name'
    ];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|between:3,64|unique:radiantweb_blog_categories',
        'code' => 'unique:radiantweb_blog_categories',
    ];

    protected $guarded = ['*'];

    public $purgeable = [
        'generate'
    ];

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
        return $this->belongsTo('Radiantweb\Problog\Models\Post', 'id', 'categories_id')->where('published',1)->orderBy('published_at', 'desc');
    }

    public function postCount()
    {
        if ($this->posts()->count()) {
            return $this->posts()->count();
        }
        else {
            return 0;
        }
    }

    public static function resolveMenuItem($item, $url, $theme)
    {

        if ($item->type == 'problog-category') {
            $Item = Array (
                'url' => static::getCategoryRenderUrl($theme,$item),
                'isActive' => 1,
                'items' => static::getBlogCategoryRenderUrls($theme,$item)
            );
        }
        else {
            $Item = Array (
                'url' => static::getCategoryRenderUrl($theme,$item),
                'isActive' => 1,
                'items' => static::getBlogCategoryRenderUrls($theme,$item,true)
            );
        }

        return $Item;
    }

    public static function getMenuTypeInfo($type)
    {
        $categories = Category::lists('slug', 'name');

        if ($type == 'problog-category') {
            $item = Array (
                'dynamicItems'  => 1,
                'nesting'       => 1,
                'references'    => $categories,
                'cmsPages'   => static::getCategoryRenderPages()
            );
        }
        else {
            $item = Array (
                'dynamicItems'  => true,
                'cmsPages'   => static::getCategoryRenderPages()
            );
        }
        return $item;
    }

    private static function getCategoryRenderUrl($theme, $item)
    {
        $category = Category::where('name',$item->reference)->first();
        $page = CmsPage::loadCached($theme, $item->cmsPage);

        // Always check if the page can be resolved
        if (!$page)
            return;

        $url = null;

        if(!$category){
            $options = ['filter'=>null,'slug' => null];
        }else{
            $options = ['filter'=>'category','slug' => $category->slug];
        }

        // Generate the URL
        $url = CmsPage::url($page->getBaseFileName(), $options , false);

        $url = Str::lower($url).'/';

        return $url;
    }

    private static function getBlogCategoryRenderUrls($theme, $item, $allcat=false)
    {
        $page = CmsPage::loadCached($theme, $item->cmsPage);
        $result = [];
        $categories = Category::lists('slug', 'name');

        $pages = [];

        if($item->nesting > 0){
            foreach($categories as $slug=>$name) {

                if($allcat){
                    $url = CmsPage::url($page->getBaseFileName(), ['filter'=>'category','slug' => $slug], false);
                    $url = Str::lower($url).'/';

                    $pages[] = array(
                        'title'=>$name,
                        'url'=>$url,
                    );
                }else{
                    $category = Category::whereRaw("LOWER(slug) = '$slug'")->first();
                    $categoryPages = Post::filterByCategory($category->id)->get()->all();

                    $pageUrl = CmsPage::url($page->getBaseFileName(), ['slug' => $slug], false);
                    $pageUrl = str_replace('/default','', Str::lower($pageUrl).'/');

                    foreach($categoryPages as $cpage){
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

    private static function getCategoryRenderPages()
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

        $cmsPages;

        return $cmsPages;
    }
/*
    public function beforeSave(){

        $post = Input::get('Category');
        $generate = $post['generate'];

        if ($generate > 0) {
            $category = $this->slug;
            $category_name = $this->name;

            $settings = ProblogSettingsModel::instance();
            $parent = $settings->get('defaultParent');

            $fileName = $category.'.htm';

            $pageDir = $parent;
            $pagePath = '/'.$parent.'/'.$category.'/';
            $pageName = $parent.'/'.$category.'.htm';
            $pageParent = $parent.'/'.$category;

            $pageDir = str_replace('/'.$category,'',$parent);

            $pages = Page::lists('baseFileName', 'baseFileName');
            $pageExists = array_key_exists($pageParent, $pages);

            if (!$pageExists && $category_name) {

                $page_content = 'title = "Blog Category '.$category_name.'"
url = "'.$pagePath.':slug?/"
layout = "default"
hidden = "0"

[proBlogPost]
post_id = ":post_id"
searchpage = "'.$parent.'"
render = "parent"
==
{% component \'proBlogPost\' %}
';

                $theme = PageTheme::getEditTheme();

                $dirPath = $theme->getPath().'/pages/'.$pageDir;

                if (!is_dir($dirPath) && !File::makeDirectory($dirPath, 0777, true, true)) {
                    throw new ApplicationException('Not able to create directory to save category page!');
                }

                if (File::put($dirPath.'/'.$fileName, $page_content) === false) {
                    throw new ApplicationException('Not able to save category html file!');
                }

            }
        }
    }
 */
}
