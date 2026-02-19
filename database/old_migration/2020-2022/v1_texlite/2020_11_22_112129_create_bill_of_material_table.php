<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained();
            $table->foreignId("material_id")->references("id")->on("products")->constrained();
            $table->double("amount",15, 8);
            $table->integer("version")->default(1)->comment("bom version");
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
        Schema::dropIfExists('bill_of_material');
    }
}
