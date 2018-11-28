<?php namespace Radiantweb\Problog\Classes;

use URL;
use DB;
use Redirect;
use Radiantweb\Problog\Classes\Twitter\TwitterOAuth;
use Session;
/**
 * print out iCal views
 * Requires min php 5.3  
 *
 * @package radiantweb/problog
 * @author ChadStrat
 */
class AuthenticateTwitter
{
    public function __construct($type=null)
    {

    }

    public static function athenticateTwitter()
    {
        $return_url = Url::to('/backend/system/settings/update/radiantweb/problog/settings');

        $pb_auth = DB::table('radiantweb_twitter_auth')->first();
        
        $oauth_token = Session::get('oauth_token');
        $oauth_token_secret = Session::get('oauth_token_secret');

         /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($pb_auth->twitter_key,$pb_auth->twitter_secret, $oauth_token, $oauth_token_secret);

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        $data = array(
            'twitter_auth_token' => $access_token['oauth_token'],
            'twitter_auth_secret' => $access_token['oauth_token_secret']
        );

        DB::table('radiantweb_twitter_auth')->where('twitter_key',$pb_auth->twitter_key)->update($data);

        return Redirect::to($return_url);
    }

}