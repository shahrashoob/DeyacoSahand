<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricRawGradingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // این جدول برای ثبت اطلاعات درجه بندی پارچه خام ایجاد شده است.
        Schema::create('fabric_raw_grading', function (Blueprint $table) {
            $table->id();
            $table->integer("section_shift_work_number")->comment("این بخش جزء چندمین شیفت کاری بوده است");
            $table->integer("section_degree_number")->comment("این بخش جزء چندمین بخش درجه بندی بوده است");
            $table->foreignId("production_id");
            $table->foreignId("production_form_id");
            $table->foreignId("production_form_item_id");
            $table->foreignId("product_id");
            $table->foreignId("degree_id");
            $table->foreignId("shift_work_id");
            $table->foreignId("carrier_id");
            $table->foreignId("lot_number_id");
            $table->integer("band_code");
            $table->float("start_point");
            $table->float("end_point");
            $table->float("amount");
            $table->float("sub_amount");
            $table->foreignId("form_id");
            $table->foreignId("form_item_id");
            $table->foreignId("status_id");
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
        Schema::dropIfExists('fabric_raw_grading');
    }
}
