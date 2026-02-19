<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeInputAlgorithmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_input_algorithm', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id");
            $table->foreignId("goods_kind_id");
            $table->foreignId("raw_material_request_algorithm_type_id")->comment("نوع الگوریتم درخواست مواد اولیه کارت های تولید");
            $table->foreignId("raw_material_request_sampling_algorithm_type_id")->comment("نوع الگوریتم درخواست مواد اولیه کارت های نمونه گیری");
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
        Schema::dropIfExists('machine_type_input_algorithm');
    }
}
