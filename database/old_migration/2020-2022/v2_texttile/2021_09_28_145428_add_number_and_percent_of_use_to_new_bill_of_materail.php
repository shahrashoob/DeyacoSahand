<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberAndPercentOfUseToNewBillOfMaterail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_bill_of_material', function (Blueprint $table) {
            $table->integer("number")->default(1)->comment("تعداد کالای مشابه که در BOM مصرف می شود.");
            $table->integer("percent_of_use")->default(100)->comment("درصد ماده اولیه که در BOM مصرف می شود.");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_bill_of_materail', function (Blueprint $table) {
            //
        });
    }
}
