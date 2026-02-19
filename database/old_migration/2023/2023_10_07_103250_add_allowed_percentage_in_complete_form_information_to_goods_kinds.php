<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageInCompleteFormInformationToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->integer("allowed_percentage_in_complete_form_information")->default(5)->
                comment("درصد اختلاف (بین مقدار وارد شده توسط پیمانکار/تامین کننده و انبار) قابل قبول جهت تکمیل و ثبت اطلاعات در انبار");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complete_form_information_to_goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
