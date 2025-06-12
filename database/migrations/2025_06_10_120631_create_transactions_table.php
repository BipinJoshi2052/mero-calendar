<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('type');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            $table->float('amount');
            $table->integer('month_value');
            $table->integer('date_value');
            $table->timestamp('created_at')->useCurrent();  // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();  // Set default and auto-update

            // Indexes
            $table->index(['user_id', 'month_value']);
            $table->index(['user_id', 'month_value', 'date_value']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
