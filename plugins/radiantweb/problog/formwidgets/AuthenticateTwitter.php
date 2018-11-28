<?php namespace Radiantweb\Problog\FormWidgets;

use DB;
use Url;
use Flash;
use Session;
use Redirect;
use Backend\Classes\FormWidgetBase;
use Radiantweb\Problog\Classes\Twitter\TwitterOAuth;

/**
 *
 * @package radiantweb\problog
 * @author ChadStrat
 */
class AuthenticateTwitter extends FormWidgetBase
{
    public $key;
    public $secret;

    /**
     * {@inheritDoc}
     */
    public function init()
    {

        $pb_auth = DB::table('radiantweb_twitter_auth')->first();

        if ($pb_auth->twitter_auth_token) {
            $connection = new TwitterOAuth(
                $pb_auth->twitter_key,
                $pb_auth->twitter_secret,
                $pb_auth->twitter_auth_token,
                $pb_auth->twitter_auth_secret
            );
            $content = $connection->get('account/verify_credentials');
            $this->vars['username'] = $username = $content->screen_name;
            $this->vars['profilepic'] = $profilepic = $content->profile_image_url;
        }

        $this->vars['reload'] = Url::to('/backend/system/settings/update/radiantweb/problog/settings');
    }

    public function onGetTwiterAuth()
    {
        $pb_auth = DB::table('radiantweb_twitter_auth')->first();

        $key = $pb_auth->twitter_key;
        $secret = $pb_auth->twitter_secret;

        $auth_url = Url::to('/radiantweb_api/problog/authenticate/twitter');

        $connection = new TwitterOAuth($key, $secret);
        
        $temporary_credentials = $connection->getRequestToken($auth_url);

        if(!isset($temporary_credentials['oauth_token'])){
            
            $p = xml_parser_create();
            xml_parse_into_struct($p,$temporary_credentials,$vals);

            Flash::error($vals[1]['value']);
            return false;
        }

        Session::put('oauth_token', $temporary_credentials['oauth_token']);
        Session::put('oauth_token_secret', $temporary_credentials['oauth_token_secret']);

        $redirect_url = $connection->getAuthorizeURL($temporary_credentials,FALSE);

        return Redirect::to($redirect_url);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('authenticatetwitter');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->model->{$this->fieldName};
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {

    }

    /*
     *  Rebuild Array to tag ID #'s
     */
    public function getSaveData($value)
    {

    }

    public function onClearTwitterAuth(){

        $pb_auth = DB::table('radiantweb_twitter_auth')->first();

        $data = array(
            'twitter_auth_token' => '',
            'twitter_auth_secret' => ''
        );

        DB::table('radiantweb_twitter_auth')->where('twitter_key',$pb_auth->twitter_key)->update($data);

        return true;
    }

}