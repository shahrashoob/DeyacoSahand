<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFactorColsToFinancialSoftwareTransKind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_software_trans_kind', function (Blueprint $table) {
            //
            $table->integer("delivery_cond_code")->nullable()->comment("کد شرایط تحویل");
            $table->integer("payment_method_code")->nullable()->comment("کد نحوه پرداخت");
            $table->integer("sales_center_code")->nullable()->comment("کد مرکز فروش");
            $table->integer("deb_side")->nullable()->comment("طرف بدهکار");
            $table->integer("calc_state")->nullable()->comment("وضعیت محاسبه کسورو اضافات و پورسانت ها");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_software_trans_kind', function (Blueprint $table) {
            //
        });
    }
}
