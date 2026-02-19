<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineInputOutputBandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_input_output_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId("production_id");
            $table->foreignId("product_id");
            $table->foreignId("lot_number_id")->nullable();
            $table->foreignId("machine_type_id");
            $table->foreignId("machine_id");
            $table->foreignId("input_band_id")->comment("باند ورودی")->nullable();
            $table->integer("input_line_code")->comment("کد خط ورودی از باند ورودی")->nullable();
            $table->integer("band_code")->comment("باند خروجی");
            $table->foreignId("material_id");
            $table->foreignId("number")->comment("تعداد در BOM");
            $table->double("amount",15,8);
            $table->integer("percent_of_use");
            $table->integer("effect_is_shared")->comment("تاثیر اشتراکی بر همبافت دارید؟")->nullable();
            $table->foreignId("allocation_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_machine_input_output_bands');
    }
}
