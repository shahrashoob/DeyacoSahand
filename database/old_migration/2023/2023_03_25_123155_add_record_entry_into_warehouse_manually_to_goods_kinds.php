<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordEntryIntoWarehouseManuallyToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->integer("record_entry_into_warehouse_manually")->default(0)->comment("ثبت ورود به انیار به صورت دستی توسط اپراتور");
            $table->integer("record_out_of_warehouse_manually")->default(0)->comment("ثبت خروج از انیار به صورت دستی توسط اپراتور");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
