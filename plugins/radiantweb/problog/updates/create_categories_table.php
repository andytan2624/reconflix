<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('radiantweb_blog_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('radiantweb_blog_post_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('categories_id')->unsigned();
            $table->primary(['post_id', 'categories_id']);
        });
    }

    public function down()
    {
        Schema::drop('radiantweb_blog_categories');
        Schema::drop('radiantweb_blog_post_categories');
    }

}
