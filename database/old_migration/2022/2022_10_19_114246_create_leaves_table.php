<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();

            $table->foreignId("post_id")->comment("پست ");
            $table->foreignId("user_id")->comment("شاغل ");

            $table->foreignId("leave_type_id")->comment("نوع مرخصی");

            $table->dateTime("start_datetime")->comment("زمان شروع مرخصی");
            $table->dateTime("end_datetime")->comment("زمان پایان مرخصی");

//            $table->integer("number_top_levels_must_confirm")->comment("تعداد سطح بالا که باید مرخصی را تایید کنند.");
//
//            $table->integer("number_top_levels_confirmed")->comment("تعدا سطخ بالا که مرخصی را تایید کردند.");

            $table->integer("status_id")->comment("وضعیت مرخصی 4630");
//
//            $table->foreignId("replace_post_id")->nullable()->comment("پست جانشین");
//            $table->foreignId("replace_user_id")->nullable()->comment("شاغل جانشین");
//
//            $table->foreignId("current_confirm_post_id")->nullable()->comment("پستی که مرخصی در انتظار تایید اوست");


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
        Schema::dropIfExists('leaves');
    }
}
