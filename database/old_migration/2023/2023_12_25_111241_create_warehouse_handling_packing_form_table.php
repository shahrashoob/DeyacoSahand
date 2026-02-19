<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseHandlingPackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_handling_packing_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_handling_id");
            $table->foreignId("packing_form_id");

            $table->foreignId("status_id")->
            comment("وضعیت خوانده شدن/نشدن بسته بندی در انبار گردانی, 524000");

            $table->timestamps();

            $table->index('warehouse_handling_id');
            $table->index('packing_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_handling_packing_form');
    }
}
