<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFaultProductFaultSignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fault_product_fault_sign', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_fault_id");
            $table->foreignId("product_fault_sign_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_fault_product_fault_sign` comment 'در این جدول مشخص می کنیم که هر نقص کالایی چه نمود هایی دارد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_fault_product_fault_sign');
    }
}
