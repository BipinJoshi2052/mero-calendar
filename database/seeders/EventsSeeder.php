<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            ['title' => 'Indra Jatra', 'month_value' => 6, 'date_value' => 15],
            ['title' => 'Bhote Jatra', 'month_value' => 6, 'date_value' => 17],
            ['title' => 'Naag Panchami', 'month_value' => 6, 'date_value' => 18],
        ]);
    }
}
