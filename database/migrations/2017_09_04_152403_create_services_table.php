<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('service_code');
            $table->string('service_name');
            $table->string('service_description');
            $table->string('service_gender');
            $table->float('service_minutes');
            $table->float('service_price');
            $table->integer('service_type_id');
            $table->string('service_picture');
            $table->text('service_data');
            $table->integer('is_active');
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
        Schema::dropIfExists('services');
    }
}
