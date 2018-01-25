<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerksTable extends Migration
{   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('perks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('perk_name');
            $table->text('perk_description')->nullable();
            $table->string('perk_picture')->nullable();
            $table->text('perk_data');
            $table->integer('perk_order');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('perks');
    }
}