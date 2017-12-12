<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlcReviewRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plc_review_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->text('valid_id_image');
            $table->string('status');
            $table->integer('updated_by_id');
            $table->datetime('processed_date')->nullable();
            $table->text('plc_review_request_data');
            $table->text('message')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('plc_review_requests');
    }
}
