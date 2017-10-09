<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('schedule_type');
            $table->datetime('date_start');
            $table->datetime('date_end');
            $table->text('schedule_data');
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
        Schema::dropIfExists('branch_schedules');
    }
}
