<?php namespace Andytan\Movies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAndytanMoviesReviews extends Migration
{
    public function up()
    {
        Schema::create('andytan_movies_reviews', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->decimal('star_rating', 10, 0)->default(0);
            $table->integer('rating')->default(0);
            $table->timestamp('published_at');
            $table->boolean('published')->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('andytan_movies_reviews');
    }
}
