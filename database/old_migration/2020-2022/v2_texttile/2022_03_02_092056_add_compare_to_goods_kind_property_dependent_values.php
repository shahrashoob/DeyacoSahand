<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompareToGoodsKindPropertyDependentValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kind_property_dependent_values', function (Blueprint $table) {
            //
            $table->string("compare")->default("=")->comment("نوع مقایسه مقادیر مشخصه ها");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kind_property_dependent_values', function (Blueprint $table) {
            //
        });
    }
}
