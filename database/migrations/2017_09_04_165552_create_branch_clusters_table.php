<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchClustersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_clusters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cluster_name');
            $table->string('cluster_owner');
            $table->string('cluster_email');
            $table->text('services');
            $table->text('products');
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
        Schema::dropIfExists('branch_clusers');
    }
}
