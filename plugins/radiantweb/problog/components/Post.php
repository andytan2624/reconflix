<?php namespace Radiantweb\Problog\Components;

use Auth;
use Mail;
use Flash;
use URL;
use Redirect;
use Cms\Classes\CmsPropertyHelper;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Radiantweb\Problog\Models\Post  as PostModel;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Radiantweb\Problog\Models\Category  as CategoryModel;
use Request;
use BackendAuth as BackendUserModel;
use System\Classes\PluginManager;

class Post extends ComponentBase
{
    public $post;
    public $slug;

    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.problog::lang.components.post.details.name',
            'description' => 'radiantweb.problog::lang.components.post.details.name',
            'post_slug' => ':post_slug'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'description' => 'radiantweb.problog::lang.components.post.properties.slug.description.',
                'title'       => 'radiantweb.problog::lang.components.post.properties.slug.title',
                'default'     => ':slug',
                'type'        => 'string'
            ],
            'searchpage' => [
                'title' => 'radiantweb.problog::lang.components.post.properties.search.title',
                'description' => 'radiantweb.problog::lang.components.post.properties.search.description',
                'type'=>'dropdown',
                'default' => ''
            ],
        ];
    }

    public function getSearchpageOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {

        $this->addCss('/plugins/radiantweb/problog/assets/css/problog_post.css');
        $this->addJs('/plugins/radiantweb/problog/assets/google-code-prettify/prettify.js');
        $this->addJs('/plugins/radiantweb/problog/assets/google-code-prettify/run_prettify.js?skin=sunburst');

        $this->getPost();

        if($this->post)
            PostModel::where('id',$this->post->id)->update(array('impressions'=>($this->post->impressions + 1)));

        if(!is_null($this->post) && ($this->post->published < 1 && !$this->user())){
            return \Response::make($this->controller->run('404'), 404);
        }

        if(!is_null($this->post) && !$this->render_category){
            /* set up metas for this post */

            $this->page['post_item'] = $this->post;

            $this->page->title = ($this->post->meta_title) ? $this->post->meta_title : $this->post->title;
            $this->page->meta_description = ($this->post->meta_description) ? $this->post->meta_description : $this->post->excerpt;
            $this->page->meta_keywords = $this->post->meta_keywords;

            $this->page['categories'] = $this->categories = CategoryModel::get()->all();

            if($this->post){
                $this->page['author'] = $this->getAuthor($this->post->user_id);
            }
            $this->page['parentPage'] = $this->post->parent;
            $this->page['searchpage'] = $this->property('searchpage');
            $this->page['back'] = Request::header('referer');
            $this->page['url'] = Request::url();

            $settings = ProblogSettingsModel::instance();
            $this->page['sharethis'] = $settings->get('sharethis');
            $this->page['facebook'] = $settings->get('facebook');
            $this->page['twitter'] = $settings->get('twitter');
            $this->page['google'] = $settings->get('google');

            $this->page['embedly'] = $settings->get('embedly');

            if($this->page['embedly']){
                $this->addJs('/plugins/radiantweb/problog/assets/js/jquery.embedly.js');
                $this->addJs('/plugins/radiantweb/problog/assets/js/embedly.js');
            }
        }
    }

     /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!PluginManager::instance()->exists('RainLab.User'))
            return null;

        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

    public function getAuthor($id)
    {
        $author = BackendUserModel::findUserById($id);
        if($author->avatar)
            $author->image = $author->avatar->getThumb(100, 100, ['mode' => 'crop']);

        return $author;
    }

    public function getPost()
    {
        if ($this->post !== null)
            return $this->post;

        $slug = $this->param('slug')?$this->param('slug'):$this->property('slug');
            
        $this->post = PostModel::where('slug','=',$slug)->first();

        if(is_null($this->post)){
            return \Response::make($this->controller->run('404'), 404);
        }else{
            return $this->post;
        }
    }
}
