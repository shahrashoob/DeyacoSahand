<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedPercentageToBeLowerInConfirmExitFormToGoodsKinds extends Migration
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
            $table->float("be_lower_in_confirm_exit_form")->default(5)->comment("درصد مجاز کمتر بودن مقدار تحویل جهت تایید فرم خروج از انبار");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('be_lower_in_confirm_exit_form_to_goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
