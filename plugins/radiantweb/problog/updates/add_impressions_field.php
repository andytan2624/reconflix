<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class AddImpressionsField extends Migration
{

    public function up()
    {
        Schema::table('radiantweb_blog_posts', function($table)
        {
            $table->text('impressions')->nullable();
        });
    }

    public function down()
    {
        Schema::table('radiantweb_blog_posts', function($table)
        {
            $table->dropColumn('impressions');
        });
    }

}