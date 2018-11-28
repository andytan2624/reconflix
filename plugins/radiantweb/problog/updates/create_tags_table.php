<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTagsTable extends Migration
{

    public function up()
    {
        Schema::create('radiantweb_blog_tags', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('radiantweb_blog_post_tags', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['post_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::drop('radiantweb_blog_tags');
        Schema::drop('radiantweb_blog_post_tags');
    }

}
