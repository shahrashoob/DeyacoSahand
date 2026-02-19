<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberToGoodsKindProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
            $table->integer("priority_number")->default(1)->comment("اولویت نمایش");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
        });
    }
}
