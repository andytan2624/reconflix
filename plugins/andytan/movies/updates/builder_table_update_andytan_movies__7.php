<?php namespace Andytan\Movies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAndytanMovies7 extends Migration
{
    public function up()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->boolean('published')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->dropColumn('published');
        });
    }
}
