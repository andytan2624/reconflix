<?php namespace Radiantweb\ProBlog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateAuthorsTable extends Migration
{
    public function up()
    {
        Schema::create('radiantweb_problog_authors', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('radiantweb_problog_authors');
    }
}
