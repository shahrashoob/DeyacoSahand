<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReservoirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_reservoir', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->index();
            $table->foreignId("reservoir_id")->index();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_reservoir` comment 'در صورتی که انبارش کالا از نوع مخزن باشد، مخزن های انتخاب شده در این جدول نگهداری می شوند.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_reservoir');
    }
}
