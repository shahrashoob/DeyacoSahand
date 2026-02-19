<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxDiffToWarehouseHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
            $table->integer("check_diff_in_amount")->
            default(0)->comment("آیا مقدار اصلی در انبارگردانی چک شود.");
            $table->integer("check_diff_in_weight")->
            default(0)->comment("آیا وزن در انبارگردانی چک شود؟  ");
            $table->integer("check_diff_in_sub_packing_form_number")->
            default(0)->comment("آیا تعداد بسته بندی فرعی در انبارگردانی چک شود.");
            $table->integer("max_diff_allowed")->default(0)->comment("حداکثر اختلاف مجاز بین مقدار بسته بندی و مقدار انبارگردانی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
        });
    }
}
