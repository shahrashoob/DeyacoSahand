<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialReplateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material_replace_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bill_of_material_id");
            $table->foreignId("product_id");
            $table->foreignId("replace_product_id")->comment("کالای جایگزین");
            $table->integer("priority_number")->comment("اولویت");
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
        Schema::dropIfExists('bill_of_material_replace_product');
    }
}
