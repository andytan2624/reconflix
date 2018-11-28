<?php namespace Radiantweb\Problog\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Cms\Classes\CmsPropertyHelper;
use Radiantweb\Problog\Models\Post as BlogPost;
use Radiantweb\Problog\Models\Series  as SeriesModel;
use Radiantweb\Problog\Models\Category  as CategoryModel;
use Radiantweb\Problog\Models\Tag  as TagModel;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use DB;
use Input;
use Redirect;
use URL;
use App;
use View;
use Request;
use BackendAuth as BackendUserModel;

class Bloglist extends ComponentBase
{
    public $posts;
    public $blogPosts;
    public $pagination;
    public $categoryPage;
    public $postPage;
    public $parentPage;
    public $currentPage;
    public $noPostsMessage;
    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.components.bloglist.details.name',
            'description' => 'radiantweb.problog::lang.components.bloglist.details.description'
        ];
    }

    public function defineProperties()
    {

        return [
            'postsPerPage' => [
                'title' => 'radiantweb.problog::lang.components.bloglist.properties.postsperpage.title',
                'default' => '10',
                'type'=>'string',
                'validationPattern'=>'^[0-9]+$',
                'validationMessage'=>'radiantweb.problog::lang.components.bloglist.properties.postsperpage.validationmessage',
                'group' => 'radiantweb.problog::lang.components.bloglist.properties.groups.pagination'
            ],
            'pagination' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.pagination.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.pagination.title',
                'type'        => 'checkbox',
                'group' => 'radiantweb.problog::lang.components.bloglist.properties.groups.pagination'
            ],
            'series' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.title',
                'default'     => '',
                'type'        => 'dropdown',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
            'filter_type' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.filter_type.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.filter_type.title',
                'default'     => 'none',
                'type'        => 'dropdown',
                'options'     => ['none'=>'none','category'=>'Category Slug','tag'=>'Tag Slug','author'=>'Author','cannonical'=>'Cannonical','popular'=>'Popular','trending'=>'Trending'],
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
            'filter_value' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.filter_value.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.filter_value.title',
                'type'=>'string',
                'default'     => '',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
            'parent' => [
                'title' => 'radiantweb.problog::lang.components.bloglist.properties.parent.title',
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.parent.description',
                'type'=>'dropdown',
                'default' => '',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
            'searchpage' => [
                'title' => 'radiantweb.problog::lang.components.categories.properties.categorypage.title',
                'description' => 'radiantweb.problog::lang.components.categories.properties.categorypage.description',
                'type'=>'dropdown',
                'default' => 'blog',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.rendering'
            ],
            'render' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.render.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.render.title',
                'default'     => 'none',
                'type'        => 'dropdown',
                'options'     => ['parent'=>'The Posts Parent','settings'=>'Default Setting','specific'=>'Specific Page'],
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.rendering'
            ],
            'specific' => [
                'title' => 'radiantweb.problog::lang.components.bloglist.properties.specific.title',
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.specific.description',
                'type'=>'dropdown',
                'default' => '',
                'depends' => ['render'],
                'placeholder' => 'Select a Page',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.rendering'
            ],
            'enable_rss' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.enable_rss.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.enable_rss.title',
                'type'        => 'checkbox',
                'group' => 'radiantweb.problog::lang.components.bloglist.properties.groups.rss'
            ],
            'rss_title' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.rss_title.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.rss_title.title',
                'type'=>'string',
                'default'     => '',
                'group' => 'radiantweb.problog::lang.components.bloglist.properties.groups.rss'
            ],
            'rss_description' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.rss_description.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.rss_description.title',
                'type'=>'string',
                'default'     => '',
                'group' => 'radiantweb.problog::lang.components.bloglist.properties.groups.rss'
            ],
        ];
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


    public function getSearchpageOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }

    public function getParentOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }


    public function getSpecificOptions()
    {
        $renderType = Request::input('render'); // Load the country property value from POST

        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $Options = [
            'none' => [],
            'settings' => [],
            'parent' => [],
            'specific' => $pages,
        ];

        return $Options[$renderType];
    }

    public function getCategories(){
        return CategoryModel::get()->all();
    }

    public function getTags(){
        return TagModel::get()->all();
    }

    public function onRun()
    {
        $this->parentPage = $this->page['parent'] = $this->property('parent')?$this->property('parent'):null;
        $this->page['rss_feed'] = Request::url().'?feed=rss';
        $this->page['enable_rss'] = $this->property('enable_rss');

        $settings = ProblogSettingsModel::instance();
        $this->blogPosts = $this->page['blogPosts'] = $this->loadPosts();
        $this->pagination = $this->page['pagination'] = $this->property('pagination');
        $this->pageParam = $this->page['pageParam'];

        $request = new Input;
        if($request->get('feed'))
            return response()->view('radiantweb.problog::rss.feed', [
                    'rss_title'=>$this->property('rss_title'),
                    'rss_description'=>$this->property('rss_title'),
                    'rss_page'=>  Request::url(),
                    'posts' =>$this->page['blogPosts'],
                    'page'=>$this->page['blogPost']
                ])->header('Content-Type', 'text/xml');


        $this->addCss('/plugins/radiantweb/problog/assets/css/blog_list.css');
    }

    public function postRender($parent)
    {
        if( $this->property('render') == 'specific' ){
            return $this->property('specific');
        }elseif( $this->property('render') == 'settings' ){
            $settings = ProblogSettingsModel::instance();
            return $settings->get('blogPost');
        }else{
            return $parent;
        }
    }

    protected function loadPosts()
    {
        //\Log::info($this->parentPage);

        $settings = ProblogSettingsModel::instance();

        $orderBySet = false;

        if($this->parentPage != ''){
            $parent = $this->parentPage;
            $BlogPosts = BlogPost::where('parent',$parent);
        }else{
            $BlogPosts = new BlogPost;
        }

        $BlogPosts = $BlogPosts->isPublished();

        /*
         * Preset Fitlers
         * First we cycle through all possible preset filtering
         * @type - category,tag,author,date-time
         */

        if ($this->property('series')){
            $series = $this->property('series');
            $BlogPosts->filterBySeries($series);
        }

        if ($this->property('filter_type') == 'category'){
            $category_name = $this->property('filter_value');
            $category = CategoryModel::where('slug', '=', $category_name)->first();
            if($category){
                $catID = $category->id;
            }else{
                $catID = '#';
                return $this->controller->run('404');
            }
            $BlogPosts->filterByCategory($catID);
        }

        if ($this->property('filter_type') == 'tag'){
            $tag = TagModel::where('slug', '=', $this->property('filter_value'))->first();
            return $tag->posts()->paginate($this->property('postsPerPage'));
        }

        if ($this->property('filter_type') == 'author'){
            $author = BackendUserModel::findUserByLogin($this->property('filter_value'));
            if($author){
                $author_id = $author->id;
            }else{
                $author_id = '#';
                return $this->controller->run('404');
            }
            $BlogPosts->filterByAuthor($author_id);
        }

        if ($this->property('filter_type') == 'cannonical'){
            if($this->param('year')){
                $y = $this->param('year');
                $m = $this->param('month');
                $d = $this->param('day');
                $BlogPosts->filterByDate($y,$m,$d);
            }
            if($this->param('filter')){
                $y = $this->param('filter');
                $m = $this->param('slug');
                $d = $this->param('instance');
                $BlogPosts->filterByDate($y,$m,$d);
            }
        }

        if ($this->property('filter_type') == 'popular'){
            $orderBySet = true;
            $BlogPosts->filterByPopular();
        }

        if ($this->property('filter_type') == 'trending'){
            $orderBySet = true;
            $BlogPosts->filterByTrending();
        }

        /*
         * Filter Request
         * Next we cycle through all possible request filters
         * @type - category,tag,author,canonical
         * (canonical requires additional different page vars /:year?/:month?/)
         */

        if($this->param('filter')){

            if($this->param('filter')==='cannonical' || is_numeric($this->param('filter'))){
                 $type = $this->param('filter');
                 $slug = $this->param('filter');
            }elseif($this->param('filter')!=='category' && !$this->param('slug')){
                $slug = $this->param('filter');
                $type = $settings->get('defaultBaseSlug')?$settings->get('defaultBaseSlug'):'category';
            }else{
                $type = $this->param('filter');
                $slug = $this->param('slug');
            }

            $desluged = $slug;
            $desluged = strtolower($desluged);
            $desluged = str_replace('-',' ',$desluged);
            $desluged = str_replace('%20',' ',$desluged);
            
            if($type == 'category'){
                $this->page['blogCurrentCategorySlug'] = $slug;
                $category = CategoryModel::where('slug', '=', $slug)->first();
                if($category){
                    $catID = $category->id;
                }else{
                    $catID = '#';
                }
                $BlogPosts->filterByCategory($catID);
            }elseif($type == 'tag'){
                $this->page['blogCurrentTagSlug'] = $slug;
                $tag = TagModel::where('slug', '=', $slug)->first();
                if($tag){
                    return $tag->posts()->paginate($this->property('postsPerPage'));
                }
                return false;
            }elseif($type == 'author'){
                $author = BackendUserModel::findUserByLogin($desluged);
                if($author){
                    $author_id = $author->id;
                }else{
                    $author_id = '#';
                }
                $BlogPosts->filterByAuthor($author_id);
            }elseif($type == 'search'){
                $this->page['search_slug'] = $desluged;
                $BlogPosts->filterBySearch($desluged);
            }elseif(is_numeric($type)){
                $y = $this->param('filter');
                $m = $this->param('slug');
                $d = $this->param('instance');
                $BlogPosts->filterByDate($y,$m,$d);
            }elseif($type == 'popular'){
                $BlogPosts->filterByPopular();
            }elseif($type == 'trending'){
                $BlogPosts->filterByTrending();
            }else{
                if($type == 'post'){
                    $slug = $this->param('filter');
                }

                $component = $this->addComponent('Radiantweb\Problog\Components\Post', 'proBlogPost', array(
                    'slug'=> $slug,
                ));
                $component->onRun();

                $this->render_post = $this->page['render_post'] = $slug;
                return $this->render_post;
            }


        }

        /**
         * if no ordering, set default ordering
         */
        if(!$orderBySet){
            $BlogPosts->orderBy('published_at', 'desc');
        }

        /*
         * no filters, we go get all
         */
        //dd($BlogPosts->toSql());
        $posts = $BlogPosts->paginate($this->property('postsPerPage'), $this->currentPage);

        return $posts;
    }

    public function getNextPosts()
    {
        $this->currentPage += 1;
        $this->blogPosts = $this->page['blogPosts'] = $this->loadPosts();
        return true;
    }
}
