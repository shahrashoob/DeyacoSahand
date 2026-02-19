<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordinationTimeForReceiveProductToAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->dateTime("coordination_time_for_receive_product")->nullable()->comment("تاریخ و زمان هماهنگی پیمانکار یا ** چهت دریافت مواد اولیه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
        });
    }
}
