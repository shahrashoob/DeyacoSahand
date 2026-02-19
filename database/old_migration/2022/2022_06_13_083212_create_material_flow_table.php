<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("bill_of_material_id");
            $table->foreignId("machine_type_id");
            $table->foreignId("bill_of_material_item_id")->comment("لاین ورودی را از طریق bom به دست می آوریم");
            $table->foreignId("band_code")->comment("باند خروجی");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `material_flows` comment 'این جدول گراف جریان مواد هر محصول را به ازای هر bom نگهداری می کند.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_flows');
    }
}
