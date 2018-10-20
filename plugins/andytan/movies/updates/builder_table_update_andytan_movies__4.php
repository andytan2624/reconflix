<?php namespace Andytan\Movies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAndytanMovies4 extends Migration
{
    public function up()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->string('slug')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('andytan_movies_', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
