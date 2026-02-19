<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeOfSaleProductProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('type_of_sale_product_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("type_of_sale_of_product_id");
            $table->foreignId("service_id")->nullable()->comment("کد محصول / کد خدمت");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `type_of_sale_product_product` comment 'به ازای هر نوع فروش، مشخص شود که کالا آن نوع فروش را دارد یا خیر'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_of_sale_product_product');
    }
}
