<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductCodeInContractorSystemToLineProductStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->string("product_code_in_contractor_system")->nullable()->comment("کد کالا در سامانه پیمانکاران");
            $table->string("service_code_in_contractor_system")->nullable()->comment("کد خدمت در سامانه پیمانکاران");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractor_system_to_line_product_station', function (Blueprint $table) {
            //
        });
    }
}
