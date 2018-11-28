<?php namespace Radiantweb\Problog\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Radiantweb\Problog\Models\Tag as TagModel;

/**
 * Optimizer SEO Tool
 *
 * @package radiantweb\problog
 * @author ChadStrat
 */
class Livetag extends FormWidgetBase
{

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
        return $this->makePartial('livetag');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->model->{$this->fieldName};
        $this->vars['existing_tags'] = TagModel::lists('name', 'name');
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/jquery.tagit.css');
        $this->addCss('css/tagit.ui-zendesk.css');
        $this->addJs('js/jquery-ui-1.9.2.custom.min.js');
        $this->addJs('js/tag-it.min.js');
    }
    
    /*
     *  Rebuild Array to tag ID #'s
     */
    public function getSaveValue($value)
    {
        $new_tag_vals = array();
        if ($value) {
            foreach($value as $tagname){
                $tag = $this->getOrCreateTag($tagname);
                $new_tag_vals[] = $tag->id;
            }
            return $new_tag_vals;
        }
    }

    /*
     *  Get Tag ID by name, or create on if not found.
     */
    protected function getOrCreateTag($tagname)
    {
        $tag = TagModel::where('name','like',$tagname)->first();

        if ($tag == null) {
            $slug = str_slug($tagname);
            $tag = TagModel::create(array('name'=>$tagname,'slug'=>$slug));
        }

        return $tag;
    }


}