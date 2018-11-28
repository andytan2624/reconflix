<?php namespace Radiantweb\Problog\Updates;

use Radiantweb\Problog\Models\Category;
use October\Rain\Database\Updates\Seeder;

class SeedAllTables extends Seeder
{

    public function run()
    {
        Category::create([
            'name' => 'Uncategorized',
            'slug' => 'uncategorized'
        ]);
    }

}