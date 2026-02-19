<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToBillOfMaterialReplace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_replace', function (Blueprint $table) {
            //
            $table->double("amount",15,8)->comment("مقدار مصرف");
            $table->integer("number")->comment("تعداد کالای مشابه که در BOM مصرف می شود.");
            $table->integer("percent_of_use")->comment("درصد ماده اولیه که در BOM مصرف می شود.");
            $table->integer("priority_number")->default(1)->comment("اولویت انتخاب");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_replace', function (Blueprint $table) {
            //
        });
    }
}
