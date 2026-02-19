<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineMaterialFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_material_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->integer("input_band_id")->comment("شناسه باند ورودی");
            $table->integer("input_line_code");
            $table->integer("goods_kind_id");
            $table->foreignId("band_code")->comment("باند خروجی");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `current_machine_material_flows` comment 'به ازای هر تخصیص یک کپی از گراف جریان در این جدول ذخیره شده و تا پایان تخصیص از آن برای نمایش خروجی ها، و تولید همبافت استفاده می گردد.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_machine_material_flows');
    }
}
