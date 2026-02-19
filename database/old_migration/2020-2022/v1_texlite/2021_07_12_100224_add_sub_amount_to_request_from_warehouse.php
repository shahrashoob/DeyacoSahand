<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubAmountToRequestFromWarehouse extends Migration
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
            $table->float("sub_amount")->default(0)->comment("مقدار فرعی مورد نیاز");
            $table->float("sub_amount_sent")->default(0)->comment("مقدار فرعی تحویل شده");
            $table->float("sub_amount_remaining")->default(0)->comment("مقدار فرعی باقی مانده");
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
