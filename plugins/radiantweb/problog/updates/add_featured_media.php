<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class AddFeaturedMedia extends Migration
{

    public function up()
    {
        Schema::table('radiantweb_blog_posts', function($table)
        {
            $table->text('featured_media')->nullable();
        });

        Schema::table('radiantweb_problog_versions', function($table)
        {
            $table->text('featured_media')->nullable();
        });
    }

    public function down()
    {

    }
}