<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingTypeLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_type_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("packing_type_id");
            $table->integer("layer_code")->comment("کد لایه در بسته بندی");
            $table->foreignId("carrier_type_id")->comment("نوع حامل مورد نیاز در لایه بسته بندی");

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
        Schema::dropIfExists('packing_type_layers');
    }
}
