<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremierLoyaltyCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premier_loyalty_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('branch_id');
            $table->string('application_type');
            $table->string('platform');
            $table->string('status');
            $table->string('reference_no');
            $table->string('remarks');
            $table->text('plc_data');
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
        Schema::dropIfExists('premier_loyalty_cards');
    }
}
