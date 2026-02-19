<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('cars', function (Blueprint $table) {

            $table->string("driver_firstname")->nullable();
            $table->string("driver_lastname");
            $table->string("driver_mobile");
            $table->string("car_plaque")->comment("پلاک خودرو");

            $table->dropColumn("driver_fullname");
            $table->dropColumn("car_tag");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
