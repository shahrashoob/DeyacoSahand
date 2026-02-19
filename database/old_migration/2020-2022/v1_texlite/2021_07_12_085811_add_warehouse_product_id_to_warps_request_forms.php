<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseProductIdToWarpsRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
            $table->foreignId("warehouse_product_id")->nullable()->comment("شماره ردیف انبار که برای تحویل انتخاب کرده است.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
        });
    }
}
