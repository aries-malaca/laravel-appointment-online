<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechnicianSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technician_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('technician_id');
            $table->datetime('date_start');
            $table->datetime('date_end');
            $table->integer('branch_id');
            $table->string('schedule_type');
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
        Schema::dropIfExists('technician_schedules');
    }
}
