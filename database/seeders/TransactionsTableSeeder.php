<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('transactions')->insert([
            ['user_id' => 1, 'title' => 'Grocery Purchase', 'type' => 1, 'category_id' => 1, 'sub_category_id' => 1, 'amount' => 50.00, 'month_value' => 6, 'date_value' => 15, 'year_value' => 2025, 'transaction_date' => '2025-06-15'],
            ['user_id' => 1, 'title' => 'Movie Ticket', 'type' => 2, 'category_id' => 3, 'sub_category_id' => 2, 'amount' => 15.00, 'month_value' => 6, 'date_value' => 17, 'year_value' => 2025, 'transaction_date' => '2025-06-17'],
            ['user_id' => 1, 'title' => 'Bus Fare', 'type' => 3, 'category_id' => 2, 'sub_category_id' => 3, 'amount' => 5.00, 'month_value' => 6, 'date_value' => 18, 'year_value' => 2025, 'transaction_date' => '2025-06-18'],
        ]);
    }
}
