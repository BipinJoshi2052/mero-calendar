<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearValueAndTransactionDateToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add 'year_value' column as an integer after 'date_value'
            $table->integer('year_value')->after('date_value');
            
            // Add 'transaction_date' column as a DATE after 'year_value'
            $table->date('transaction_date')->nullable()->after('year_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the 'year_value' and 'transaction_date' columns if rolling back
            $table->dropColumn('year_value');
            $table->dropColumn('transaction_date');
        });
    }
}
