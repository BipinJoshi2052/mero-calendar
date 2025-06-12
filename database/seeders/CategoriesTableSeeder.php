<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            ['title' => 'Food','type' => 0],
            ['title' => 'Transportation','type' => 0],
            ['title' => 'Housing','type' => 0],
            ['title' => 'Entertainment','type' => 0],
            ['title' => 'Healthcare','type' => 0],
            ['title' => 'Other','type' => 0],
            ['title' => 'Salary','type' => 1],
            ['title' => 'Business','type' => 1],
            ['title' => 'Investment','type' => 1],
            ['title' => 'Other','type' => 1]
        ]);
    }
}
