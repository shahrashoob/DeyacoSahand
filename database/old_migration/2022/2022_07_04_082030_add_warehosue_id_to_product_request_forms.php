<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehosueIdToProductRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
            $table->foreignId("warehouse_id")->nullable()->comment("انبار درخواست کالا، به ازای هر درخواست تمام آیتم ها از همان انبار تامین می شوند.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
        });
    }
}
