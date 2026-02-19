<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinNumberOfPackingFormToProductRequestForm extends Migration
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
            $table->integer("min_number_of_packing_forms")->nullable()->after("amount_remaining")->comment("حداقل تعداد بسته بندی که در زمان تحویل سفارش باید تحویل شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_form', function (Blueprint $table) {
            //
        });
    }
}
