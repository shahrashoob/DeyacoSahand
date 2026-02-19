<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordinateDateTimeToProductRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
            $table->dateTime("coordinate_date_time")->nullable()->comment("تاریخ و زمان هماهنگی جهت ارسال کالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
        });
    }
}
