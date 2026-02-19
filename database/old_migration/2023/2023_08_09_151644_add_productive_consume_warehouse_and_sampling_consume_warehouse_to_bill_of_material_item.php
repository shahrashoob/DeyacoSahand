<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductiveConsumeWarehouseAndSamplingConsumeWarehouseToBillOfMaterialItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->foreignId("productive_consume_warehouse_type_id")->default(2)->comment("نوع انبار مصرف کالای تولیدی");
            $table->foreignId("productive_consume_warehouse_id")->nullable()->comment(" انبار مصرف کالای تولیدی");

            $table->foreignId("sampling_consume_warehouse_type_id")->default(2)->comment("نوع انبار مصرف کالای نمونه گیری");
            $table->foreignId("sampling_consume_warehouse_id")->nullable()->comment(" انبار مصرف کالای نمونه گیری");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
        });
    }
}
