<?php namespace Radiantweb\Problog\Components;

use Cms\Classes\ComponentBase;
use Radiantweb\Problog\Models\Post as BlogPost;
use Radiantweb\Problog\Models\Series  as SeriesModel;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Cms\Classes\CmsPropertyHelper;
use Cms\Classes\Page;
use Request;
use App;
use DB;

class Archive extends ComponentBase
{
    public $Archive;
    public $ArchivePage;
    public $currentArchiveSlug;
    
    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.components.archive.details.name',
            'description' => 'radiantweb.problog::lang.components.archive.details.description'
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
                'title' => 'radiantweb.problog::lang.components.archive.properties.parent.title',
                'description' => 'radiantweb.problog::lang.components.archive.properties.parent.description',
                'type'=>'dropdown',
                'default' => '',
                'group'=>'radiantweb.problog::lang.components.archive.properties.groups.filter'
            ],
            'render' => [
                'description' => 'radiantweb.problog::lang.components.archive.properties.render.description',
                'title'       => 'radiantweb.problog::lang.components.archive.properties.render.title',
                'default'     => 'parent',
                'type'        => 'dropdown',
                'options'     => ['parent'=>'The Posts Parent','settings'=>'Default Setting','specific'=>'Specific Page'],
                'group'=>'radiantweb.problog::lang.components.archive.properties.groups.rendering'
            ],
            'specific' => [
                'title' => 'radiantweb.problog::lang.components.archive.properties.specific.title',
                'description' => 'radiantweb.problog::lang.components.archive.properties.specific.description',
                'type'=>'dropdown',
                'default' => '',
                'depends' => ['render'],
                'placeholder' => 'Select a Page',
                'group'=>'radiantweb.problog::lang.components.archive.properties.groups.rendering'
            ],
        ];
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

    public function getArchivePageOptions()
    {
        return CmsPropertyHelper::listPages();;
    }

    public function getParentOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
        
        $ParentOptions = array_merge($ParentOptions, $pages);

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

        $this->archive = $this->page['blogArchive'] = $this->loadArchive();
        $settings = ProblogSettingsModel::instance(); 
        $this->page['blogPost'] = $settings->get('blogPost');
    
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

    protected function loadArchive()
    {
        $this->page['min_date'] = BlogPost::min('published_at');
        
        if($this->property('parent') != ''){
            $BlogPosts = BlogPost::where('parent',$this->property('parent'));
        }else{
            $BlogPosts = new BlogPost;
        }

        $BlogPosts = $BlogPosts->isPublished();
        
        if ($this->property('series')){
            $series = $this->property('series');
            $BlogPosts->filterBySeries($series);
        }
        
        //dd($BlogPosts->toSql());
        
        return $BlogPosts->orderBy('published_at','desc')->get()->all();
        
    }
}