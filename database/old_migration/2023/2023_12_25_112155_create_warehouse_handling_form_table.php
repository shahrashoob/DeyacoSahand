<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseHandlingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_handling_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_handling_id");
            $table->foreignId("input_form_id")->comment("فرم انبارگردانی برای ورود به انبار");
            $table->foreignId("output_form_id")->comment("فرم انبارگردانی برای خروج از  انبار");
            $table->timestamps();

            $table->index('warehouse_handling_id');
            $table->index('input_form_id');
            $table->index('output_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_handling_form');
    }
}
