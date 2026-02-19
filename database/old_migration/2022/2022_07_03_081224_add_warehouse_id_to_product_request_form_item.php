<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToProductRequestFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
            $table->foreignId("warehouse_id")->nullable()->comment("انبار درخواست کالا");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
        });
    }
}
