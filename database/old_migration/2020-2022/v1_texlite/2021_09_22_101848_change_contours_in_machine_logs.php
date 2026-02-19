<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeContoursInMachineLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_logs', function (Blueprint $table) {
            $table->float("contour_1_value",15,2)->change();
            $table->float("contour_2_value",15,2)->change();
            $table->float("contour_3_value",15,2)->change();
            $table->float("contour_4_value",15,2)->change();
            $table->float("contour_5_value",15,2)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_logs', function (Blueprint $table) {
            //
        });
    }
}
