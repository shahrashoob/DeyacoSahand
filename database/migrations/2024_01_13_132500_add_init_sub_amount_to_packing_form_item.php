<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitSubAmountToPackingFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
            $table->double("init_sub_amount",15,7)->nullable()->after("sub_amount");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
        });
    }
}
