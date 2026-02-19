<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialOperationPatternItemTypeIdToFinancialOperationPatternItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_operation_pattern_item', function (Blueprint $table) {
            //
            $table->foreignId("financial_operation_pattern_item_type_id")->default(1)->comment("نوع حساب مرتبط: اصلی، ارزش افزوده، تخفیف و ...");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_operation_pattern_item', function (Blueprint $table) {
            //
        });
    }
}
