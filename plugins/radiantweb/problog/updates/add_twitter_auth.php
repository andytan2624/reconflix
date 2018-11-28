<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class AddTwitterAuth extends Migration
{

    public function up()
    {
        Schema::create('radiantweb_twitter_auth', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('twitter_key')->index();
            $table->string('twitter_secret')->nullable();
            $table->string('twitter_auth_token')->nullable();
            $table->string('twitter_auth_secret')->nullable();
            $table->timestamps();
        });

        $data = [
            'twitter_key' => 'MfUJJrhZDXHUsvbVSf2Ag',
            'twitter_secret' => "uhvYCtKCNSdHGYvwNwu80rkw5Ju53f5jhaLPMAgK0",
            'twitter_auth_token' => '',
            'twitter_auth_secret' => ''
        ];

        DB::table('radiantweb_twitter_auth')->insert($data);
    }

    public function down()
    {
        Schema::drop('radiantweb_twitter_auth');
    }

}