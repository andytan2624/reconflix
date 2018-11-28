<?php namespace Radiantweb\Problog\Classes;

/**
 * print out iCal views
 * Requires min php 5.3  
 *
 * @package radiantweb/problog
 * @author ChadStrat
 */
class CheckUrlManager
{
    public function __construct($type=null)
    {

    }
    
    public static function checkUrl(){
        $link = $_REQUEST['link'];
        $file_headers = @get_headers($link);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = 0;
        }
        else {
            $exists = 1;
        }
        \Log::info('checkURL ran');

        print json_encode(array('valid_url'=>$exists));
    }

    public static function checkUrlXmlrpc(){
        $link = $_REQUEST['link'];
        $file_headers = @get_headers($link);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = 0;
        }
        else {
            $exists = 1;
        }

        $xmlrpc = 0;
        $postText = null;
        $ch = curl_init();
        $timeout = 25;
        curl_setopt($ch,CURLOPT_URL,$link);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $postText = curl_exec($ch);
        curl_close($ch);
        
        if (preg_match('/<link rel="pingback" href="([^"]+)"/',$postText,$server)){
          // It has the <LINK> tag!
            //print  $server[1] ;
            $xmlrpc = 1;
        }

        print json_encode(array('valid_url'=>$exists,'xmlrpc'=>$xmlrpc));
    }

}