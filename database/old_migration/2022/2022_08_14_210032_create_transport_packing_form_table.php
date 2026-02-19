<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportPackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_packing_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("transport_id");
            $table->foreignId("transport_item_id");
            $table->foreignId("packing_form_id");
            $table->foreignId("status_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `transports` comment 'کلاس موقت ویژه دوره پیاده سازی جهت ثبت اطلاعات عدل بندی'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_packing_form');
    }
}
