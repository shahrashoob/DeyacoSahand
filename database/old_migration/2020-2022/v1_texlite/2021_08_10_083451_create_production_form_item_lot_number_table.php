<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFormItemLotNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // هر فرم ایتم می تواند یک یا چند لات داشته باشد که در این جدول ذخیر گردید و هنگام درجه بندی استفاده می گردد.
        Schema::create('production_form_item_lot_number', function (Blueprint $table) {
            $table->id();
            $table->foreignId("production_form_id");
            $table->foreignId("production_form_item_id");
            $table->foreignId("lot_number_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_form_item_lot_number');
    }
}
