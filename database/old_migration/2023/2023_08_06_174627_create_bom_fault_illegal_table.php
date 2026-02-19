<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomFaultIllegalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material_fault_illegal', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bill_of_material_id");
            $table->foreignId("bill_of_material_item_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("product_fault_id");
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
        Schema::dropIfExists('bom_fault_illegal');
    }
}
