<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialDegreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material_degree', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bill_of_material_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id")->comment("کد کالای ماده اولیه");
            $table->foreignId("degree_id");
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
        Schema::dropIfExists('bill_of_material_degree');
    }
}
