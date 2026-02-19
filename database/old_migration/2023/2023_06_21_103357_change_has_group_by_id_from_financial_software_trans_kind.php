<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeHasGroupByIdFromFinancialSoftwareTransKind extends Migration
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
            $table->renameColumn("has_group_by_ic","has_group_by_product");
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
