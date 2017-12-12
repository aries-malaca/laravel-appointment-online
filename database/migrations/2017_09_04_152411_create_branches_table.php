<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_name');
            $table->string('branch_code');
            $table->integer('region_id');
            $table->integer('city_id');
            $table->text('branch_address');
            $table->string('branch_email');
            $table->string('branch_contact');
            $table->string('branch_contact_person')->nullable();
            $table->datetime('opening_date')->nullable();
            $table->integer('rooms_count');
            $table->text('social_media_accounts');
            $table->text('directions')->nullable();
            $table->string('map_coordinates');
            $table->string('branch_classification');
            $table->text('payment_methods')->nullable();
            $table->text('welcome_message')->nullable();
            $table->text('branch_pictures');
            $table->text('branch_data');
            $table->integer('cluster_id');
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
        Schema::dropIfExists('branches');
    }
}
