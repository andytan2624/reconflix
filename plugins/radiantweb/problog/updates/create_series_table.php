<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateSeriesTable extends Migration
{
    public function up()
    {
        
        Schema::create('radiantweb_problog_series', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->timestamps();
        });

        Schema::table('radiantweb_blog_posts', function($table)
        {
            $table->integer('series_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('radiantweb_problog_series');
    }
}
