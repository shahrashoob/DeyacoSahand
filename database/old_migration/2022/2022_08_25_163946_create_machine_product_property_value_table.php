<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineProductPropertyValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_product_property_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id");
            $table->foreignId("product_id");
            $table->foreignId("station_id");
            $table->foreignId("machine_product_property_id");
            $table->string("value");
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
        Schema::dropIfExists('machine_product_property_value');
    }
}
