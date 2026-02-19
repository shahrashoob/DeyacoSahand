<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarpsRequestFormItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warps_request_form_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warps_request_form_id");
            $table->foreignId("production_id");
            $table->foreignId("product_id");
            $table->foreignId("warehouse_product_id");
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
        Schema::dropIfExists('warps_request_form_item');
    }
}
