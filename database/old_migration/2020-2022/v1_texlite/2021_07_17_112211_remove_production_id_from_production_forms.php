<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveProductionIdFromProductionForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
            $table->dropColumn("production_id");
            $table->dropColumn("product_id");
            $table->dropColumn("lot_number_id");
            $table->dropColumn("amount");
            $table->dropColumn("sub_amount");
            $table->dropColumn("amount_after_control");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
        });
    }
}
