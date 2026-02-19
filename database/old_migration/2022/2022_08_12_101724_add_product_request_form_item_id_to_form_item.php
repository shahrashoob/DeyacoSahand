<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductRequestFormItemIdToFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //
            $table->foreignId("product_request_form_item_id")->nullable()->comment("در فرم خروج از انبار، مشخص شود که به ازای هر ایتم درخواست چقدر تحویل داده ایم.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //
        });
    }
}
