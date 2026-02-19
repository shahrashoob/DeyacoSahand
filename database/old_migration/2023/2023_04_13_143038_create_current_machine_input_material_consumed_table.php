<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineInputMaterialConsumedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_input_material_consumed', function (Blueprint $table) {
            $table->id();
            $table->foreignId("current_machine_input_id");
            $table->foreignId("current_machine_input_log_id");
            $table->foreignId("machine_id");
            $table->foreignId("machine_log_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("lot_number_id");
            $table->foreignId("packing_form_item_id");
            $table->double("amount_consumed",10,5);
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
        Schema::dropIfExists('current_machine_input_material_consumed');
    }
}
