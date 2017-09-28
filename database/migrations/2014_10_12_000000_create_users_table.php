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
            $table->string('middle_name')->nullable();
            $table->datetime('birth_date');
            $table->string('user_mobile')->nullable();
            $table->string('gender');
            $table->integer('level');
            $table->string('user_address')->nullable();
            $table->text('user_data')->nullable();
            $table->text('device_data')->nullable();
            $table->datetime('last_activity')->nullable();
            $table->datetime('last_login')->nullable();
            $table->integer('is_client');
            $table->integer('is_confirmed');
            $table->integer('is_agreed');
            $table->integer('is_active');
            $table->string('user_picture')->nullable();
            $table->rememberToken();
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
