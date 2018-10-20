<?php namespace Andytan\Movies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAndytanMovies extends Migration
{
    public function up()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->timestamp('deleted_at');
            $table->increments('id')->unsigned(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
            $table->dropColumn('deleted_at');
            $table->increments('id')->unsigned()->change();
        });
    }
}
