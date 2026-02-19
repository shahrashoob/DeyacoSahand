<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderConsumedProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_consumed_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id");
            $table->foreignId("order_list_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("contractor_supply_type_id");
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
        Schema::dropIfExists('order_consumed_product');
    }
}
