<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class CreatePostsTable extends Migration
{

    public function up()
    {
        Schema::create('radiantweb_blog_posts', function($table)
        {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            //$table->integer('series_id')->nullable();
            $table->integer('categories_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('parent')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->datetime('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
        });

         DB::statement('ALTER TABLE radiantweb_blog_posts ADD FULLTEXT search(title, content)');
    }

    public function down()
    {
        Schema::drop('radiantweb_blog_posts');
    }

}
