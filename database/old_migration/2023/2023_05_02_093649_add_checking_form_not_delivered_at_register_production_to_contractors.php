<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckingFormNotDeliveredAtRegisterProductionToContractors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
            $table->integer("checking_form_not_delivered_at_register_production")->default(0)->
            comment("آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار چک شود، (در صورت T بودن، باید همه بسته بندی ها تحویل به انبار شده باشند.)");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
        });
    }
}
