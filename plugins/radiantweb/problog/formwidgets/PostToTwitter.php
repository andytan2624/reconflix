<?php namespace Radiantweb\Problog\FormWidgets;

use DB;
use URL;
use Backend\Classes\FormWidgetBase;
use Radiantweb\Problog\Models\Settings as ProblogSettingsModel;
use Radiantweb\Problog\Classes\Twitter\TwitterOAuth;

/**
 *
 * @package radiantweb\problog
 * @author ChadStrat
 */
class PostToTwitter extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $pb_auth = DB::table('radiantweb_twitter_auth')->first();
        $this->vars['twitter_auth_token'] = $pb_auth->twitter_auth_token;
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('posttotwitter');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->model->{$this->fieldName} ? $this->model->{$this->fieldName} : 0;
        $this->vars['name'] = $this->formField->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {

    }
    
    /**
     * Rebuild Array to tag ID #'s
     */
    public function getSaveValue($value) 
    {

    }

    function onDoTweet()
    {
        $settings = ProblogSettingsModel::instance(); 
        $blogPost = $settings->get('blogPost'); 

        $pb_auth = DB::table('radiantweb_twitter_auth')->first();

        $PB_AUTH_TOKEN = $pb_auth->twitter_auth_token;
        $PB_AUTH_SECRET = $pb_auth->twitter_auth_secret;
        $PB_APP_KEY = $pb_auth->twitter_key;
        $PB_APP_SECRET = $pb_auth->twitter_secret;

        if ($PB_AUTH_TOKEN) {
            $connection = new TwitterOAuth(
                $PB_APP_KEY,
                $PB_APP_SECRET,
                $PB_AUTH_TOKEN,
                $PB_AUTH_SECRET
            );

            $url = Url::to('/').'/'.$blogPost.'/'.$this->model->slug.'/';
            $msg = str_replace('{{url}}', $url, $_REQUEST['message']);
            $update_status = $connection->post('statuses/update', ['status' => $msg]);
        }
    }

}