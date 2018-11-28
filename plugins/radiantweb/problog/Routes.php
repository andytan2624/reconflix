<?php
use \Radiantweb\Problog\Classes\CheckUrlManager;
use \Radiantweb\Problog\Classes\AuthenticateTwitter;

Route::group(['prefix' => 'radiantweb_api/problog/check_url/'], function() {
    Route::get('valid', function(){ return View::make('radiantweb.problog::check_url.valid', ['valid' => CheckUrlManager::checkUrl()]); });
    Route::get('valid_link', function(){ return View::make('radiantweb.problog::check_url.valid', ['valid' => CheckUrlManager::checkUrlXmlrpc()]); });
});

Route::get('/radiantweb_api/problog/authenticate/twitter', function(){ return AuthenticateTwitter::athenticateTwitter(); });
?>