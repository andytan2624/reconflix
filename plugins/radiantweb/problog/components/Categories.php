<?php namespace Radiantweb\Problog\Components;

use Cms\Classes\ComponentBase;
use Radiantweb\Problog\Models\Series  as SeriesModel;
use Radiantweb\Problog\Models\Category as BlogCategory;
use Cms\Classes\CmsPropertyHelper;
use Cms\Classes\Page;
use Request;
use App;
use DB;

class Categories extends ComponentBase
{
    public $categories;
    public $categoryPage;
    public $currentCategorySlug;
    
    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.components.categories.details.name',
            'description' => 'radiantweb.problog::lang.components.categories.details.description'
        ];
    }

    public function defineProperties()
    {
        return [
	        'series' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.title',
                'default'     => '',
                'type'        => 'dropdown',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
	        'parent' => [
                'title' => 'radiantweb.problog::lang.components.categories.properties.parent.title',
                'description' => 'radiantweb.problog::lang.components.categories.properties.parent.description',
                'type'=>'dropdown',
                'default' => '',
                'group'=>'radiantweb.problog::lang.components.categories.properties.groups.filter'
            ],
            'categoryPage' => [
                'title' => 'radiantweb.problog::lang.components.categories.properties.categorypage.title',
                'description' => 'radiantweb.problog::lang.components.categories.properties.categorypage.description',
                'type'=>'dropdown',
                'default' => '',
                'group'=>'radiantweb.problog::lang.components.categories.properties.groups.rendering'
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
    
    public function getParentOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
        
        $ParentOptions = array_merge($ParentOptions, $pages);
        
        //\Log::info($ParentOptions);
        return $ParentOptions;
    }
    
    public function getSeriesOptions()
    {
        $SeriesOptions = array(''=>'-- chose one --');
        $series = SeriesModel::select('id', 'name')->get()->all();
        foreach($series as $s){
	        $SeriesOptions[$s->id] = $s->name;
        }

        //\Log::info($ParentOptions);
        return $SeriesOptions;
    }

    public function onRun()
    {

        $this->categories = $this->page['blogCategories'] = $this->loadCategories();
        $this->categoryPage = $this->page['blogCategoryPage'] = $this->property('categoryPage');

    }

    protected function loadCategories()
    {
        $categories = BlogCategory::orderBy('name');
        
        if($this->property('parent') == ''){
            $categories->whereExists(function($query) {
                $query->select(DB::raw(1))
                ->from('radiantweb_blog_posts')
                ->whereNotNull('radiantweb_blog_posts.published')
                ->where('published', 1)
                ->whereRaw('radiantweb_blog_categories.id = radiantweb_blog_posts.categories_id');
                
                if ($this->property('series')){
		            $series = $this->property('series');
		            $query->whereRaw("radiantweb_blog_posts.series_id = '$series'");
		        }
            });
        }else{
            $categories->whereExists(function($query) {

                $parent = $this->property('parent');
                
                $query->select(DB::raw(1))
                ->from('radiantweb_blog_posts')
                ->whereNotNull('radiantweb_blog_posts.published')
                ->where('parent',$parent)
                ->where('published', 1)
                ->whereRaw('radiantweb_blog_categories.id = radiantweb_blog_posts.categories_id');
                
                if ($this->property('series')){
		            $series = $this->property('series');
		            $query->where("radiantweb_blog_posts.series_id = '$series'");
		        }
            });
        }

        return $categories->get()->all();
    }
}