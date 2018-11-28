<?php namespace Radiantweb\Problog\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Radiantweb\Problog\Models\Version as BlogVersion;
use Radiantweb\Problog\Models\Post as BlogPost;
use Radiantweb\Problog\Models\Category as BlogCategory;
use Radiantweb\Problog\Models\Series as BlogSeries;
use Backend\Models\User as BackendUserModel;
/**
 * PostHistory Form Widget
 */
class PostHistory extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'radiantweb_problog_post_history';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('posthistory');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/posthistory.css', 'Radiantweb.Problog');
        $this->addJs('js/posthistory.js', 'Radiantweb.Problog');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        return $value;
    }

    public function getLoadValue()
    {
        $version_history = BlogVersion::where('post_id','=',$this->model->id)->orderBy('version', 'ASC')->get()->all();
        return $version_history;
    }

    public function onRestoreVersion()
    {
        //get the version model
        $new_model = BlogVersion::where('post_id','=',$this->model->id)->where('version','=',$_REQUEST['version'])->first();

        \Log::info($new_model);

        //fetch the current post model
        $current_model = BlogPost::find($this->model->id);

        //update the current post model properties to the old version and save
        $current_model->user = BackendUserModel::find($new_model->user_id);
        $current_model->series = BlogSeries::find($new_model->series_id);
        $current_model->categories = BlogCategory::find($new_model->categories_id);
        $current_model->title = $new_model->title;
        $current_model->slug = $new_model->slug;
        $current_model->parent = $new_model->parent;
        $current_model->excerpt = $new_model->excerpt;
        $current_model->content = $new_model->content;
        $current_model->content_markdown = $new_model->content_markdown;
        $current_model->featured_media = $new_model->featured_media;
        $current_model->published_at = $new_model->published_at;
        $current_model->published = $new_model->published;
        $current_model->meta_title = $new_model->meta_title;
        $current_model->meta_description = $new_model->meta_description;
        $current_model->meta_keywords = $new_model->meta_keywords;
        $current_model->save();

        //remove any version after the version Model
        BlogVersion::where('version','>',$_REQUEST['version'])->delete();

        return ['success'=> true];
    }
}
