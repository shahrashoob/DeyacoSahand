<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupingInTheDisplayProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_display_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id");
            $table->foreignId("goods_kind_property_id")->comment("مشخه ای که بر اساس آن کالا ها در رسته کالایی دسته بندی می شوند.");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `goods_kind_display_properties` comment 'در این جدول مشخص می شود هر رسته کالایی بر اساس کدام مشخصه دسته بندی می شود. (سامانه فروش)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grouping_in_the_display_product');
    }
}
