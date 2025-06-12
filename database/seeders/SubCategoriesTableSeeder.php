<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('sub_categories')->insert([
            ['title' => 'Groceries', 'category_id' => 1],
            ['title' => 'Restaurants', 'category_id' => 1],
            ['title' => 'Snacks', 'category_id' => 1],
            ['title' => 'Movies', 'category_id' => 1],

            ['title' => 'Gas', 'category_id' => 2],
            ['title' => 'Public Transport', 'category_id' => 2],
            ['title' => 'Taxi', 'category_id' => 2],

            ['title' => 'Rent', 'category_id' => 3],
            ['title' => 'Utilities', 'category_id' => 3],
            ['title' => 'Maintenance', 'category_id' => 3],

            ['title' => 'Movies', 'category_id' => 4],
            ['title' => 'Games', 'category_id' => 4],
            ['title' => 'Travel', 'category_id' => 4],
            
            ['title' => 'Medicine', 'category_id' => 5],
            ['title' => 'Doctor', 'category_id' => 5],
            ['title' => 'Insurance', 'category_id' => 5],

            ['title' => 'Clothing', 'category_id' => 6],
            ['title' => 'Education', 'category_id' => 6],
            ['title' => 'Misc', 'category_id' => 6],

            ['title' => 'Regular Salary', 'category_id' => 7],
            ['title' => 'Bonus', 'category_id' => 7],
            ['title' => 'Overtime', 'category_id' => 7],

            ['title' => 'Sales', 'category_id' => 8],
            ['title' => 'Services', 'category_id' => 8],
            ['title' => 'Investment', 'category_id' => 8],

            ['title' => 'Dividends', 'category_id' => 9],
            ['title' => 'Interest', 'category_id' => 9],
            ['title' => 'Capital Gains', 'category_id' => 9],

            ['title' => 'Gift', 'category_id' => 10],
            ['title' => 'Freelance', 'category_id' => 10],
            ['title' => 'Misc', 'category_id' => 10]
        ]);
    }
}
