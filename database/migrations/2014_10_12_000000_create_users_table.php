<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password');
            $table->string('username');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->datetime('birth_date');
            $table->string('mobile');
            $table->string('gender');
            $table->integer('level');
            $table->string('address');
            $table->text('branch_data');
            $table->text('user_data');
            $table->text('device_data');
            $table->datetime('registered_date');
            $table->datetime('last_activity');
            $table->datetime('last_login');
            $table->integer('is_confirmed');
            $table->integer('is_active');
            $table->string('picture');
            $table->rememberToken();
            $table->string('access_token');
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
        Schema::dropIfExists('users');
    }
}
