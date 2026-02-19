<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineInputMaterialDegreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_input_material_degree', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("degree_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `current_machine_input_material_degree` comment 'در این جدول اطلاعات درجه های BOM ذخیره می گردد، چون ممکن است، بعد از تخصیص BOM تغیر کند.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_machine_input_material_degree');
    }
}
