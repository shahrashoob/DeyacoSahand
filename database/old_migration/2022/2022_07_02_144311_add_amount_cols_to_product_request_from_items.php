<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountColsToProductRequestFromItems extends Migration
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
            $table->double("amount_request",15,8)->nullable()->comment("مقدار درخواست");
            $table->double("amount_sent",15,8)->default(0)->comment("مقدار ارسال شده");
            $table->double("amount_remaining",15,8)->nullable()->comment("مقدار باقی مانده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_from_items', function (Blueprint $table) {
            //
        });
    }
}
