<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisterDetailedCodeForCustomerToFinancialOperationPatterns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_operation_patterns', function (Blueprint $table) {
            //
            $table->integer("register_detailed_code_for_customer")->default(0)->comment("آیا کد تفصیلی مشتری ثبت گردد");
            $table->integer("detailed_code_for_customer_number")->nullable()->comment("کد تفصیلی مشتری در تفصیلی چند ثبت گردد");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_operation_patterns', function (Blueprint $table) {
            //
        });
    }
}
