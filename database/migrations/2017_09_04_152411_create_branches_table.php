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
            $table->text('branch_emails');
            $table->text('branch_contacts');
            $table->datetime('opening_date');
            $table->integer('rooms_count');
            $table->string('social_media_accounts');
            $table->text('directions');
            $table->string('map_coordinates');
            $table->string('map_picture');
            $table->text('operating_schedules');
            $table->string('branch_classification');
            $table->text('payment_methods');
            $table->text('welcome_message');
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
