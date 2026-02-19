<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSumWpcToRequestFromWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_from_warehouse', function (Blueprint $table) {
            //
            $table->double("sum_wpc")->default(0)->comment("sum wpc");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_from_warehouse', function (Blueprint $table) {
            //
        });
    }
}
