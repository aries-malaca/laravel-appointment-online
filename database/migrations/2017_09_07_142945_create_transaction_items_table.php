<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id');
            $table->integer('item_id');
            $table->string('item_type');
            $table->float('amount');
            $table->integer('quantity');
            $table->datetime('book_start_time')->nullable();
            $table->datetime('book_end_time')->nullable();
            $table->datetime('serve_start_time')->nullable();
            $table->datetime('serve_end_time')->nullable();
            $table->datetime('complete_time')->nullable();
            $table->string('item_status');
            $table->text('item_data');
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
        Schema::dropIfExists('transaction_items');
    }
}
