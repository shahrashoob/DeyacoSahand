<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindPropertyDependentValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('goods_kind_property_dependent_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_property_id");
            $table->foreignId("goods_kind_property_parent_id");
            $table->string("parent_value");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_property_dependent_values` comment 'این جدول جهت نگهداری مقادیر مشخصه های وابسته می باشد، یعنی مشخصه پدر باید چه ویژگی هایی داشته باشد تا مشخه فرزند قابل نمایش باشد '");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_property_dependent_values');
    }
}
