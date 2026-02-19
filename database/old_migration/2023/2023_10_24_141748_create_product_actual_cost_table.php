<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductActualCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_actual_cost', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("packing_type_id");
            $table->double("price",15,2)->comment("قیمت بروز (ریال)");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_actual_cost` comment 'قیمت روز کالا - نوع بسته بندی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_actaul_cost');
    }
}
