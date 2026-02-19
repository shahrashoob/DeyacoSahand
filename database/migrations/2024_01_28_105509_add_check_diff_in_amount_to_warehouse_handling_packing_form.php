<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckDiffInAmountToWarehouseHandlingPackingForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_handling_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("has_diff_in_amount")->default(0)->comment("مقدار اختلاف مقدار نهایی بسته بندی و مقدار نهایی اعلام شده در انبارگردانی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('amount_to_warehouse_handling_packing_form', function (Blueprint $table) {
            //
        });
    }
}
