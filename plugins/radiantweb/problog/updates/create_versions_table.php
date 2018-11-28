<?php namespace Radiantweb\Problog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateVersionsTable extends Migration
{
    public function up()
    {
        Schema::create('radiantweb_problog_versions', function(Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('version')->unsigned();
            $table->integer('post_id')->nullable()->index();
            $table->integer('current_post_id')->nullable()->index();
            $table->integer('user_id')->unsigned();
            $table->integer('series_id')->nullable();
            $table->integer('categories_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('parent')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('content_markdown')->nullable();
            $table->datetime('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('radiantweb_problog_versions');
    }
}
