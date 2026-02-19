<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiffToWarehouseHandlingPackingForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_handling_packing_form', function (Blueprint $table) {
            $table->double("final_amount", 15, 7)->nullable()->comment("مقدار نهایی بسته بندی در انبار گردانی");
            $table->double("weight", 15, 6)->nullable()->comment("مقدار نهایی بسته بندی در انبار گردانی");
            $table->integer("sub_packing_form_number")->nullable()->comment("تعداد بسته بندی های فرعی در انبار گردانی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_handling_packing_form', function (Blueprint $table) {
            //
        });
    }
}
