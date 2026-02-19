<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderListPackingTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_list_packing_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id");
            $table->foreignId("order_list_id");
            $table->foreignId("packing_type_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `order_list_packing_type` comment 'به ازای هر ردیف درخواست ممکن است بیش از یک بسته بندی انتخاب شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_list_packing_type');
    }
}
