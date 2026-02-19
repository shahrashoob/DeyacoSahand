<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasGroupByIcToFinancialSoftwareTransKind extends Migration
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
            $table->foreignId("has_group_by_ic")->default(0)->
            comment("به تفکیک آیتم بسته بندی (اگر F باشد به تفکیک مرکز هزینه تراکنش ثبت می شود.)");
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
