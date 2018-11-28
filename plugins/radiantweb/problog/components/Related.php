<?php namespace Radiantweb\Problog\Components;

use Cms\Classes\ComponentBase;
use Radiantweb\Problog\Models\Post as BlogPost;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Radiantweb\Problog\Models\Tag as TagModel;
use Radiantweb\Problog\Models\Series  as SeriesModel;
use Radiantweb\Problog\Models\Category as CategoryModel;
use Cms\Classes\CmsPropertyHelper;
use Cms\Classes\Page;
use Request;
use App;
use DB;

class Related extends ComponentBase
{
    public $Related;
    public $RelatedPage;
    public $currentRelatedSlug;

    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.components.related.details.name',
            'description' => 'radiantweb.problog::lang.components.related.details.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsPerPage' => [
                'title' => 'Number of Related Posts ',
                'default' => '4',
                'type'=>'string',
                'validationPattern'=>'^[0-9]+$',
                'validationMessage'=>'radiantweb.problog::lang.components.bloglist.properties.postsperpage.validationmessage',
            ],
            'descriptionSize' => [
                'title' => 'Description size',
                'default' => '220',
                'type'=>'string',
                'validationPattern'=>'^[0-9]+$',
                'validationMessage'=>'radiantweb.problog::lang.components.bloglist.properties.postsperpage.validationmessage',
            ],
            'series' => [
                'description' => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.description',
                'title'       => 'radiantweb.problog::lang.components.bloglist.properties.filter_series.title',
                'default'     => '',
                'type'        => 'dropdown',
                'group'=>'radiantweb.problog::lang.components.bloglist.properties.groups.filter'
            ],
            'parent' => [
                'title' => 'radiantweb.problog::lang.components.related.properties.parent.title',
                'description' => 'radiantweb.problog::lang.components.related.properties.parent.description',
                'type'=>'dropdown',
                'default' => '',
                'group'=>'radiantweb.problog::lang.components.related.properties.groups.filter'
            ],
            'render' => [
                'description' => 'radiantweb.problog::lang.components.related.properties.render.description',
                'title'       => 'radiantweb.problog::lang.components.related.properties.render.title',
                'default'     => 'parent',
                'type'        => 'dropdown',
                'options'     => ['parent'=>'The Posts Parent','settings'=>'Default Setting','specific'=>'Specific Page'],
                'group'=>'radiantweb.problog::lang.components.related.properties.groups.rendering'
            ],
            'specific' => [
                'title' => 'radiantweb.problog::lang.components.related.properties.specific.title',
                'description' => 'radiantweb.problog::lang.components.related.properties.specific.description',
                'type'=>'dropdown',
                'default' => '',
                'depends' => ['render'],
                'placeholder' => 'Select a Page',
                'group'=>'radiantweb.problog::lang.components.related.properties.groups.rendering'
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

    public function getRelatedPageOptions()
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
        $this->page['descriptionSize'] = $this->property('descriptionSize');
        $this->Related = $this->page['blogRelated'] = $this->loadRelated();
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

    protected function loadRelated()
    {
        /**
         * - check the current url params
         * - resolve the filter type
         * - if post slug, get posts tags and loop
         * - if other, grab associations and loop
         */
        if($this->param('filter')){

            if(
                $this->param('filter')!=='tag' &&
                $this->param('filter')!=='category' &&
                $this->param('filter')!=='author' &&
                $this->param('filter')!=='search' &&
                $this->param('filter')!=='cannonical' &&
                !is_numeric($this->param('filter'))
            ){
                $slug = $this->param('slug')?$this->param('slug'):$this->param('filter');
                $BlogPost = BlogPost::where('slug','=',$slug)->first();
 
                $tagModel = new TagModel;
                $tagModel = $tagModel->getRelatedTagsBaseQuery();
                /*
                 * base this query for tags on the current post
                 */

                if(!$BlogPost)
                    return false;

                $tagModel->where('radiantweb_blog_posts.id','!=', $BlogPost->id);
                
                
                if ($this->property('series')){
		            $series = $this->property('series');
		            $tagModel->where('radiantweb_blog_posts.series_id','!=', $series);
		        }

                if($BlogPost->tags){
                    $i = 0;
                    $tagIDs = array();
                    /*
                     * loop through each of the current posts tags
                     */
                    foreach($BlogPost->tags as $t){
                        $tagIDs[] = $t->id;
                        $i++;
                    }
                    /*
                     * filter posts by each tag id
                     */
                    $tagModel->whereIn("radiantweb_blog_post_tags.tag_id",$tagIDs);
                    /*
                     * do not repeat post id's
                     */

                    $query = $tagModel->groupBy('id')->orderBy('impressions', 'desc')->limit($this->property('postsPerPage'));
                    //dd($query->toSql());
                    return  $query->get()->all();
                }
            }else{
                return false;
            }

        }
        return false;
    }
}