<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no');
            $table->integer('branch_id');
            $table->integer('client_id');
            $table->datetime('transaction_datetime');
            $table->string('transaction_status');
            $table->string('platform');
            $table->string('booked_by_name');
            $table->integer('booked_by_id');
            $table->string('booked_by_type');
            $table->text('waiver_data');
            $table->text('transaction_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
